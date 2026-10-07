<?php

namespace App\Http\Controllers;

use App\Services\PaymentConfirmationService;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JsonException;

class PaystackWebhookController extends Controller
{
    public function __invoke(Request $request, PaystackService $paystack, PaymentConfirmationService $confirmation)
    {
        $rawBody = $request->getContent();
        $signature = $request->header('x-paystack-signature');

        if (!$paystack->hasValidWebhookSignature($rawBody, $signature)) {
            Log::warning('Paystack webhook signature rejected.');

            return response('Unauthorized', 401);
        }

        try {
            $event = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            Log::warning('Paystack webhook contained invalid JSON.');

            return response('Invalid event', 400);
        }

        if (!is_array($event)) {
            return response('Invalid event', 400);
        }

        if (($event['event'] ?? null) !== 'charge.success') {
            return response('OK', 200);
        }

        $reference = $event['data']['reference'] ?? null;
        if (!is_string($reference) || $reference === '') {
            Log::warning('Paystack charge.success webhook omitted its reference.');

            return response('OK', 200);
        }

        try {
            $result = $confirmation->confirm($reference);

            if ($result['status'] === 'not_found') {
                Log::warning('Paystack webhook referenced an unknown order.', [
                    'paystack_reference' => $reference,
                ]);
            } elseif ($result['status'] === 'invalid') {
                Log::warning('Paystack webhook transaction did not match its order.', [
                    'paystack_reference' => $reference,
                ]);
            }
        } catch (\Throwable $exception) {
            Log::error('Paystack webhook processing failed.', [
                'paystack_reference' => $reference,
                'exception_type' => $exception::class,
            ]);

            return response('Processing failed', 500);
        }

        return response('OK', 200);
    }
}