<?php

namespace App\Http\Controllers;

use App\Models\EmployerPaymentOrder;
use App\Services\PaymentConfirmationService;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaystackPaymentController extends Controller
{
    public function __construct(
        private readonly PaystackService $paystack,
        private readonly PaymentConfirmationService $confirmation
    ) {}

    public function createOrder(Request $request): RedirectResponse
    {
        $this->ensureEmployer($request);
        $packages = config('employer_plans', []);
        $standardPlans = array_keys(array_filter(
            $packages,
            fn (array $package): bool => ($package['amount'] ?? null) !== null
        ));

        $validated = $request->validate([
            'package' => ['required', 'string', Rule::in($standardPlans)],
            'payment_method' => ['required', Rule::in(['mpesa', 'card', 'bank_transfer'])],
            'mpesa_phone' => [
                Rule::requiredIf($request->input('payment_method') === 'mpesa'),
                'nullable',
                'string',
                'max:32',
            ],
        ]);

        $phone = null;
        if ($validated['payment_method'] === 'mpesa') {
            $phone = PaystackService::normalizeKenyanPhone($validated['mpesa_phone'] ?? '');

            if ($phone === null) {
                return back()->withErrors([
                    'mpesa_phone' => 'Enter a valid Kenyan M-Pesa number, such as 0712 345 678.',
                ])->withInput();
            }
        }

        $package = $packages[$validated['package']];
        $order = EmployerPaymentOrder::query()->create([
            'user_id' => $request->user()->id,
            'order_reference' => (string) Str::uuid(),
            'plan' => $validated['package'],
            'amount' => $package['amount'],
            'job_allowance' => $package['job_allowance'],
            'duration_unit' => $package['duration_unit'],
            'duration_value' => $package['duration_value'],
            'currency' => config('services.paystack.currency', 'KES'),
            'payment_method' => $validated['payment_method'],
            'paystack_phone' => $phone,
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);

        if ($order->payment_method === 'bank_transfer') {
            return redirect()->route('employer.payment.pending', $order->order_reference);
        }

        return $this->initiate($request, $order);
    }

    public function initiate(Request $request, EmployerPaymentOrder $order): RedirectResponse
    {
        $this->ensureOrderOwner($request, $order);

        if ($order->status === 'paid') {
            return redirect()->route('employer.payment.pending', $order->order_reference);
        }

        if ($order->status !== 'pending' || ($order->expires_at && $order->expires_at->isPast())) {
            if ($order->status === 'pending' && $order->expires_at?->isPast()) {
                $order->forceFill(['status' => 'expired'])->save();
            }

            return redirect()->route('employer.payment.pending', $order->order_reference)
                ->with('payment_error', 'This order is no longer available. Choose a plan to start a new checkout.');
        }

        if (!$this->orderSnapshotMatches($order)) {
            Log::warning('Paystack order snapshot is invalid.', ['order_reference' => $order->order_reference]);

            return redirect()->route('employer.payment.pending', $order->order_reference)
                ->with('payment_error', 'The plan details could not be verified. Return to pricing and start a new checkout.');
        }

        if ($order->payment_method === 'card' && $order->paystack_authorization_url) {
            return redirect()->away($order->paystack_authorization_url);
        }

        if ($order->paystack_reference && $order->paystack_initiated_at) {
            $existing = $this->confirmation->confirm($order->paystack_reference, $request->user()->id);

            if ($existing['status'] === 'paid') {
                return redirect()->route('employer.payment.pending', $order->order_reference);
            }

            if ($existing['status'] === 'pending') {
                return redirect()->route('employer.payment.pending', $order->order_reference)
                    ->with('payment_error', 'A payment attempt is already being checked. Do not start another charge.');
            }

            if (in_array($existing['status'], ['invalid', 'expired', 'not_found'], true)) {
                return redirect()->route('employer.payment.pending', $order->order_reference)
                    ->with('payment_error', 'The existing Paystack attempt needs review; a second charge was not started.');
            }
        }

        $reference = DB::transaction(function () use ($order): ?string {
            $lockedOrder = EmployerPaymentOrder::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->status !== 'pending' || $lockedOrder->paystack_reference !== null) {
                return null;
            }

            $reference = strtolower(Str::random(32));
            $lockedOrder->forceFill([
                'paystack_reference' => $reference,
                'paystack_initiated_at' => now(),
                'paystack_status' => 'initializing',
            ])->save();

            return $reference;
        });

        if ($reference === null) {
            return redirect()->route('employer.payment.pending', $order->order_reference)
                ->with('payment_error', 'A payment attempt is already in progress. Check its status before retrying.');
        }

        $order->refresh();

        $amount = (string) ($order->amount * (int) config('services.paystack.subunit_multiplier', 100));
        $metadata = [
            'order_reference' => $order->order_reference,
            'plan' => $order->plan,
        ];

        if ($order->payment_method === 'card') {
            $result = $this->paystack->initializeTransaction([
                'email' => $request->user()->email,
                'amount' => $amount,
                'currency' => $order->currency,
                'reference' => $reference,
                'callback_url' => route('paystack.callback'),
                'channels' => ['card'],
                'metadata' => $metadata,
            ]);

            $data = $result['data'] ?? [];
            $authorizationUrl = $data['authorization_url'] ?? null;
            $returnedReference = $data['reference'] ?? null;

            if (!$result['ok'] || !is_string($authorizationUrl) || $returnedReference !== $reference || !$this->isPaystackCheckoutUrl($authorizationUrl)) {
                $this->recordInitializationFailure($order, $reference, $result);

                return redirect()->route('employer.payment.pending', $order->order_reference)
                    ->with('payment_error', 'Paystack could not start card checkout. Your order remains pending; retry in a moment.');
            }

            $order->forceFill([
                'paystack_authorization_url' => $authorizationUrl,
                'paystack_access_code' => is_string($data['access_code'] ?? null) ? $data['access_code'] : null,
                'paystack_status' => 'initialized',
            ])->save();

            return redirect()->away($authorizationUrl);
        }

        if ($order->payment_method !== 'mpesa' || !$order->paystack_phone) {
            return redirect()->route('employer.payment.pending', $order->order_reference)
                ->with('payment_error', 'This payment method is not available for this order.');
        }

        $result = $this->paystack->chargeMobileMoney([
            'email' => $request->user()->email,
            'amount' => $amount,
            'currency' => $order->currency,
            'reference' => $reference,
            'metadata' => $metadata,
            'mobile_money' => [
                'phone' => $order->paystack_phone,
                'provider' => 'mpesa',
            ],
        ]);

        $data = $result['data'] ?? [];
        $gatewayStatus = is_string($data['status'] ?? null) ? $data['status'] : null;

        if (!$result['ok'] || ($data['reference'] ?? null) !== $reference || $gatewayStatus === null) {
            if ($result['ok']) {
                $result['failure_type'] = 'malformed_response';
                $result['gateway_status'] = $gatewayStatus;
            }

            $this->recordInitializationFailure($order, $reference, $result);

            return redirect()->route('employer.payment.pending', $order->order_reference)
                ->with('payment_error', $this->mpesaInitializationError($result));
        }

        $order->forceFill([
            'paystack_status' => $gatewayStatus,
            'paystack_display_text' => is_string($data['display_text'] ?? null)
                ? $data['display_text']
                : 'Authorize the payment prompt on your phone. The plan remains inactive until Paystack confirms it.',
        ])->save();

        if ($gatewayStatus === 'success') {
            $this->confirmation->confirm($reference, $request->user()->id);
        } elseif (!in_array($gatewayStatus, ['pay_offline', 'pending'], true)) {
            $order->forceFill(['status' => 'failed'])->save();
        }

        return redirect()->route('employer.payment.pending', $order->order_reference);
    }

    public function retry(Request $request, EmployerPaymentOrder $order): RedirectResponse
    {
        $this->ensureOrderOwner($request, $order);

        if ($order->status === 'failed') {
            $order->forceFill([
                'status' => 'pending',
                'paystack_reference' => null,
                'paystack_access_code' => null,
                'paystack_authorization_url' => null,
                'paystack_display_text' => null,
                'paystack_status' => null,
                'paystack_initiated_at' => null,
            ])->save();
        }

        return $this->initiate($request, $order->refresh());
    }

    public function checkStatus(Request $request, EmployerPaymentOrder $order): RedirectResponse
    {
        $this->ensureOrderOwner($request, $order);

        if ($order->paystack_reference) {
            $result = $this->confirmation->confirm($order->paystack_reference, $request->user()->id);

            if ($result['status'] === 'failed') {
                return redirect()->route('employer.payment.pending', $order->order_reference)
                    ->with('payment_error', 'Paystack reports this payment failed. You can retry this order.');
            }

            if ($result['status'] === 'pending') {
                return redirect()->route('employer.payment.pending', $order->order_reference)
                    ->with('payment_pending', 'Paystack has not confirmed this payment yet. Your plan is still inactive.');
            }

            if ($result['status'] === 'expired') {
                return redirect()->route('employer.payment.pending', $order->order_reference)
                    ->with('payment_error', 'This order expired before payment was confirmed. Choose a plan to start a new checkout.');
            }
        }

        return redirect()->route('employer.payment.pending', $order->order_reference);
    }

    public function callback(Request $request, PaymentConfirmationService $confirmation): RedirectResponse
    {
        $reference = $request->query('reference');
        abort_unless(is_string($reference) && $reference !== '', 400);

        $order = EmployerPaymentOrder::query()
            ->where('user_id', $request->user()->id)
            ->where('paystack_reference', $reference)
            ->firstOrFail();

        $result = $confirmation->confirm($reference, $request->user()->id);
        $redirect = redirect()->route('employer.payment.pending', $order->order_reference);

        return match ($result['status']) {
            'paid' => $redirect->with('payment_success', 'Payment verified. Your subscription is active.'),
            'failed' => $redirect->with('payment_error', 'Paystack reports the payment failed. You can retry this order.'),
            'invalid' => $redirect->with('payment_error', 'The Paystack transaction did not match this order and was not activated.'),
            default => $redirect->with('payment_pending', 'Payment is not confirmed yet. Your subscription is not active.'),
        };
    }

    private function orderSnapshotMatches(EmployerPaymentOrder $order): bool
    {
        $plan = config("employer_plans.{$order->plan}");

        return is_array($plan)
            && $order->plan !== 'enterprise'
            && $plan['amount'] === $order->amount
            && $plan['job_allowance'] === $order->job_allowance
            && $plan['duration_unit'] === $order->duration_unit
            && $plan['duration_value'] === $order->duration_value
            && $order->currency === config('services.paystack.currency', 'KES');
    }

    private function isPaystackCheckoutUrl(string $url): bool
    {
        $parts = parse_url($url);

        return ($parts['scheme'] ?? null) === 'https'
            && ($parts['host'] ?? null) === 'checkout.paystack.com';
    }

    private function recordInitializationFailure(EmployerPaymentOrder $order, string $reference, array $result): void
    {
        Log::error('Paystack payment initialization failed.', [
            'payment_order_id' => $order->id,
            'order_reference' => $order->order_reference,
            'paystack_reference' => $reference,
            'payment_method' => $order->payment_method,
            'failure_type' => $result['failure_type'] ?? 'unknown',
            'http_status' => $result['http_status'] ?? null,
            'paystack_status' => $result['paystack_status'] ?? null,
            'paystack_message' => $result['paystack_message'] ?? null,
            'gateway_status' => $result['gateway_status'] ?? null,
            'diagnostic_code' => $result['diagnostic_code'] ?? null,
            'exception_type' => $result['exception_type'] ?? null,
            'exception_code' => $result['exception_code'] ?? null,
            'transport_message' => $result['transport_message'] ?? null,
        ]);

        $fields = ['paystack_status' => 'initialization_failed'];

        if (!($result['uncertain'] ?? false)) {
            $fields['paystack_reference'] = null;
            $fields['paystack_initiated_at'] = null;
        }

        $order->forceFill($fields)->save();
    }

    private function mpesaInitializationError(array $result): string
    {
        return match ($result['failure_type'] ?? null) {
            'configuration' => 'M-Pesa payments are unavailable because Paystack is not configured. Please contact support.',
            'timeout' => 'Paystack did not respond in time. Your payment was not started; please retry shortly.',
            'connection_error', 'request_exception' => 'We could not contact Paystack. Your payment was not started; please retry shortly.',
            'malformed_response' => 'Paystack returned an unexpected response. Your payment was not started; please retry shortly.',
            default => 'Paystack could not start the M-Pesa request. Confirm your number and try again. If this continues, contact support with your order reference.',
        };
    }

    private function ensureEmployer(Request $request): void
    {
        abort_unless($request->user()?->account_type === 'employer', 403);
    }

    private function ensureOrderOwner(Request $request, EmployerPaymentOrder $order): void
    {
        $this->ensureEmployer($request);
        abort_unless($order->user_id === $request->user()->id, 404);
    }
}
