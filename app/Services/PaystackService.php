<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    public function initializeTransaction(array $payload): array
    {
        return $this->request('post', '/transaction/initialize', $payload);
    }

    public function chargeMobileMoney(array $payload): array
    {
        return $this->request('post', '/charge', $payload);
    }

    public function verifyTransaction(string $reference): array
    {
        return $this->request('get', '/transaction/verify/' . rawurlencode($reference));
    }

    public function hasValidWebhookSignature(string $rawBody, ?string $signature): bool
    {
        $secret = config('services.paystack.webhook_secret')
            ?: config('services.paystack.secret_key');

        if (!is_string($secret) || $secret === '' || !is_string($signature) || $signature === '') {
            return false;
        }

        return hash_equals(hash_hmac('sha512', $rawBody, $secret), $signature);
    }

    public static function normalizeKenyanPhone(string $phone): ?string
    {
        $phone = preg_replace('/[\s().-]+/', '', $phone) ?? '';

        if (str_starts_with($phone, '0')) {
            $phone = '+254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '254')) {
            $phone = '+' . $phone;
        } elseif (str_starts_with($phone, '7') || str_starts_with($phone, '1')) {
            $phone = '+254' . $phone;
        }

        return preg_match('/^\+254[17][0-9]{8}$/', $phone) === 1
            ? $phone
            : null;
    }

    private function request(string $method, string $path, ?array $payload = null): array
    {
        $secret = config('services.paystack.secret_key');
        $baseUrl = rtrim((string) config('services.paystack.payment_url'), '/');

        if (!is_string($secret) || $secret === '' || $baseUrl === '') {
            return [
                'ok' => false,
                'failure_type' => 'configuration',
                'error' => 'Paystack is not configured.',
                'data' => null,
            ];
        }

        try {
            $request = Http::baseUrl($baseUrl)
                ->withToken($secret)
                ->acceptJson()
                ->asJson()
                ->connectTimeout(5)
                ->timeout(15);

            $response = $method === 'get'
                ? $request->get($path)
                : $request->post($path, $payload ?? []);

            $body = $response->json();

            if (!is_array($body)) {
                return [
                    'ok' => false,
                    'failure_type' => 'malformed_response',
                    'error' => 'Paystack returned an invalid response.',
                    'http_status' => $response->status(),
                    'data' => null,
                ];
            }

            $data = $body['data'] ?? null;
            $diagnostics = [
                'http_status' => $response->status(),
                'paystack_status' => is_bool($body['status'] ?? null) ? $body['status'] : null,
                'paystack_message' => $this->sanitizeDiagnosticMessage($body['message'] ?? null),
                'gateway_status' => is_array($data) ? $this->diagnosticValue($data['status'] ?? null) : null,
                'diagnostic_code' => $this->diagnosticValue($body['code'] ?? (is_array($data) ? ($data['code'] ?? null) : null)),
            ];

            if (!$response->successful()) {
                return [
                    'ok' => false,
                    'failure_type' => 'http_error',
                    'error' => 'Paystack rejected the request.',
                    'data' => null,
                    ...$diagnostics,
                ];
            }

            if (($body['status'] ?? null) !== true) {
                return [
                    'ok' => false,
                    'failure_type' => 'api_rejected',
                    'error' => 'Paystack rejected the request.',
                    'data' => null,
                    ...$diagnostics,
                ];
            }

            if (!is_array($data)) {
                return [
                    'ok' => false,
                    'failure_type' => 'malformed_response',
                    'error' => 'Paystack returned an invalid response.',
                    'data' => null,
                    ...$diagnostics,
                ];
            }

            return [
                'ok' => true,
                'data' => $data,
                'message' => $diagnostics['paystack_message'] ?? '',
                ...$diagnostics,
            ];
        } catch (ConnectionException $exception) {
            return [
                'ok' => false,
                'failure_type' => $this->isTimeout($exception) ? 'timeout' : 'connection_error',
                'uncertain' => true,
                'error' => 'Paystack is temporarily unavailable.',
                'data' => null,
                'exception_type' => $exception::class,
                'exception_code' => $this->diagnosticValue($exception->getCode()),
                'transport_message' => $this->sanitizeTransportMessage($exception->getMessage()),
            ];
        } catch (\Throwable $exception) {
            Log::warning('Paystack API request failed.', [
                'endpoint' => $path,
                'exception_type' => $exception::class,
            ]);

            return [
                'ok' => false,
                'failure_type' => 'request_exception',
                'uncertain' => true,
                'error' => 'Paystack is temporarily unavailable.',
                'data' => null,
                'exception_type' => $exception::class,
                'exception_code' => $this->diagnosticValue($exception->getCode()),
                'transport_message' => $this->sanitizeTransportMessage($exception->getMessage()),
            ];
        }
    }

    private function isTimeout(ConnectionException $exception): bool
    {
        return preg_match('/(?:timed out|timeout|cURL error 28)/i', $exception->getMessage()) === 1;
    }

    private function diagnosticValue(mixed $value): ?string
    {
        if (!is_string($value) && !is_int($value)) {
            return null;
        }

        $value = (string) $value;

        return preg_match('/^[A-Za-z0-9_.-]{1,64}$/', $value) === 1
            ? $value
            : null;
    }

    private function sanitizeDiagnosticMessage(mixed $message): ?string
    {
        if (!is_string($message) || $message === '') {
            return null;
        }

        $message = preg_replace('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i', '[redacted-email]', $message) ?? '';
        $message = preg_replace('/(?<!\d)(?:\+?254|0)[\s().-]*[17](?:[\s().-]*\d){8}(?!\d)/', '[redacted-phone]', $message) ?? '';
        $message = preg_replace('/(?:sk|pk)_(?:test|live)_[A-Za-z0-9_-]+/', '[redacted-key]', $message) ?? '';
        $message = preg_replace('/(?:Authorization:\s*Bearer\s+|Bearer\s+)[^\s,]+/i', '[redacted-authorization]', $message) ?? '';
        $message = trim(preg_replace('/\s+/', ' ', $message) ?? '');

        return $message === '' ? null : substr($message, 0, 500);
    }

    private function sanitizeTransportMessage(string $message): ?string
    {
        return $this->sanitizeDiagnosticMessage($message);
    }
}
