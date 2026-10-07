<?php

namespace App\Services;

use App\Models\EmployerPaymentOrder;
use App\Models\EmployerSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentConfirmationService
{
    public function __construct(private readonly PaystackService $paystack) {}

    public function confirm(string $reference, ?int $ownerId = null): array
    {
        $order = EmployerPaymentOrder::query()
            ->where('paystack_reference', $reference)
            ->first();

        if (!$order || ($ownerId !== null && $order->user_id !== $ownerId)) {
            return ['status' => 'not_found', 'order' => null];
        }

        if ($order->status === 'paid') {
            return ['status' => 'paid', 'order' => $order->load('subscription')];
        }

        if ($order->status !== 'pending') {
            return ['status' => $order->status, 'order' => $order];
        }

        if ($order->expires_at && $order->expires_at->isPast()) {
            $order->forceFill(['status' => 'expired'])->save();

            return ['status' => 'expired', 'order' => $order->refresh()];
        }

        if (!$this->hasValidSnapshot($order)) {
            Log::warning('Paystack order snapshot rejected.', [
                'order_reference' => $order->order_reference,
                'paystack_reference' => $reference,
            ]);

            return ['status' => 'invalid', 'order' => $order];
        }

        $verification = $this->paystack->verifyTransaction($reference);

        if (!$verification['ok']) {
            Log::warning('Paystack transaction verification unavailable.', [
                'order_reference' => $order->order_reference,
                'paystack_reference' => $reference,
            ]);

            return ['status' => 'pending', 'order' => $order];
        }

        $transaction = $verification['data'];
        $gatewayStatus = $transaction['status'] ?? null;

        if ($gatewayStatus !== 'success') {
            if (in_array($gatewayStatus, ['failed', 'abandoned'], true)) {
                $order->forceFill([
                    'status' => 'failed',
                    'paystack_status' => $gatewayStatus,
                ])->save();

                return ['status' => 'failed', 'order' => $order->refresh()];
            }

            $order->forceFill(['paystack_status' => is_string($gatewayStatus) ? $gatewayStatus : 'pending'])->save();

            return ['status' => 'pending', 'order' => $order->refresh()];
        }

        if (!$this->transactionMatchesOrder($transaction, $order, $reference)) {
            Log::warning('Paystack transaction details did not match the pending order.', [
                'order_reference' => $order->order_reference,
                'paystack_reference' => $reference,
            ]);

            return ['status' => 'invalid', 'order' => $order];
        }

        return DB::transaction(function () use ($order, $transaction, $reference): array {
            $lockedOrder = EmployerPaymentOrder::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedOrder->status === 'paid') {
                return ['status' => 'paid', 'order' => $lockedOrder->load('subscription')];
            }

            if ($lockedOrder->status !== 'pending' || $lockedOrder->paystack_reference !== $reference) {
                return ['status' => $lockedOrder->status, 'order' => $lockedOrder];
            }

            if ($lockedOrder->expires_at && $lockedOrder->expires_at->isPast()) {
                $lockedOrder->forceFill(['status' => 'expired'])->save();

                return ['status' => 'expired', 'order' => $lockedOrder->refresh()];
            }

            $startsAt = now();
            $expiresAt = $lockedOrder->duration_unit === 'months'
                ? $startsAt->copy()->addMonths($lockedOrder->duration_value)
                : $startsAt->copy()->addDays($lockedOrder->duration_value);

            $subscription = EmployerSubscription::query()->create([
                'user_id' => $lockedOrder->user_id,
                'plan' => $lockedOrder->plan,
                'amount' => $lockedOrder->amount,
                'payment_method' => $lockedOrder->payment_method,
                'status' => 'successful',
                'transaction_reference' => $reference,
                'paid_at' => $transaction['paid_at'] ?? $startsAt,
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'job_allowance' => $lockedOrder->job_allowance,
            ]);

            $lockedOrder->forceFill([
                'status' => 'paid',
                'paystack_status' => 'success',
                'paid_at' => $transaction['paid_at'] ?? $startsAt,
                'verified_at' => $startsAt,
                'subscription_id' => $subscription->id,
            ])->save();

            Log::info('Paystack payment confirmed.', [
                'order_reference' => $lockedOrder->order_reference,
                'paystack_reference' => $reference,
                'subscription_id' => $subscription->id,
            ]);

            return ['status' => 'paid', 'order' => $lockedOrder->refresh()->load('subscription')];
        });
    }

    private function hasValidSnapshot(EmployerPaymentOrder $order): bool
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

    private function transactionMatchesOrder(array $transaction, EmployerPaymentOrder $order, string $reference): bool
    {
        return ($transaction['reference'] ?? null) === $reference
            && (int) ($transaction['amount'] ?? -1) === $order->amount * (int) config('services.paystack.subunit_multiplier', 100)
            && ($transaction['currency'] ?? null) === $order->currency;
    }
}