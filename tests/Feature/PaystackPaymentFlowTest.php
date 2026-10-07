<?php

namespace Tests\Feature;

use App\Models\EmployerPaymentOrder;
use App\Models\EmployerSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaystackPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.paystack.secret_key' => 'sk_test_fake_only',
            'services.paystack.webhook_secret' => 'sk_test_fake_only',
            'services.paystack.payment_url' => 'https://api.paystack.co',
            'services.paystack.currency' => 'KES',
        ]);
    }

    public function test_card_checkout_initializes_with_server_amount_and_redirects_to_paystack(): void
    {
        $user = $this->employer();
        Http::fake(function (ClientRequest $request) {
            $reference = $request['reference'];

            return Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/test-checkout',
                    'access_code' => 'test-access-code',
                    'reference' => $reference,
                ],
            ]);
        });

        $response = $this->actingAs($user)->post(route('employer.payment.order'), [
            'package' => 'growth',
            'payment_method' => 'card',
            'amount' => 1,
            'job_allowance' => 999,
        ]);

        $response->assertRedirect('https://checkout.paystack.com/test-checkout');
        $order = EmployerPaymentOrder::query()->firstOrFail();
        $this->assertSame(20000, $order->amount);
        $this->assertSame(20, $order->job_allowance);
        $this->assertSame('KES', $order->currency);
        $this->assertSame('pending', $order->status);
        $this->assertNotNull($order->paystack_reference);
        $this->assertDatabaseMissing('employer_subscriptions', ['user_id' => $user->id]);

        Http::assertSent(fn (ClientRequest $request): bool => str_ends_with($request->url(), '/transaction/initialize')
            && $request['amount'] === '2000000'
            && $request['currency'] === 'KES'
            && $request['reference'] === $order->paystack_reference
            && $request['metadata']['order_reference'] === $order->order_reference);
    }

    public function test_mpesa_charge_normalizes_phone_and_waits_for_offline_authorization(): void
    {
        $user = $this->employer();
        Http::fake(function (ClientRequest $request) {
            return Http::response([
                'status' => true,
                'message' => 'Charge attempted',
                'data' => [
                    'reference' => $request['reference'],
                    'status' => 'pay_offline',
                    'display_text' => 'Approve the payment prompt on your phone.',
                ],
            ]);
        });

        $response = $this->actingAs($user)->post(route('employer.payment.order'), [
            'package' => 'basic',
            'payment_method' => 'mpesa',
            'mpesa_phone' => '0712 345 678',
        ]);

        $order = EmployerPaymentOrder::query()->firstOrFail();
        $response->assertRedirect(route('employer.payment.pending', $order->order_reference));
        $this->assertSame('+254712345678', $order->paystack_phone);
        $this->assertSame('pay_offline', $order->paystack_status);
        $this->assertSame('pending', $order->status);
        $this->assertSame(0, $user->employerSubscriptions()->count());
        $this->get(route('employer.payment.pending', $order->order_reference))
            ->assertOk()
            ->assertSee('Approve the payment prompt on your phone.');

        Http::assertSent(fn (ClientRequest $request): bool => str_ends_with($request->url(), '/charge')
            && $request['email'] === $user->email
            && $request['mobile_money']['phone'] === '+254712345678'
            && $request['mobile_money']['provider'] === 'mpesa'
            && $request['amount'] === '300000'
            && $request['currency'] === 'KES');
    }

    public function test_missing_paystack_secret_key_does_not_send_an_mpesa_charge(): void
    {
        $user = $this->employer();
        config(['services.paystack.secret_key' => null]);
        Http::fake();

        $response = $this->actingAs($user)->post(route('employer.payment.order'), [
            'package' => 'basic',
            'payment_method' => 'mpesa',
            'mpesa_phone' => '0712 345 678',
        ]);

        $order = EmployerPaymentOrder::query()->firstOrFail();
        $response->assertRedirect(route('employer.payment.pending', $order->order_reference));
        $response->assertSessionHas('payment_error', 'M-Pesa payments are unavailable because Paystack is not configured. Please contact support.');
        $this->assertSame('pending', $order->status);
        $this->assertSame('initialization_failed', $order->paystack_status);
        $this->assertNull($order->paystack_reference);
        $this->assertSame(0, $user->employerSubscriptions()->count());
        Http::assertNothingSent();
    }

    public function test_invalid_mpesa_phone_is_rejected_before_order_or_gateway_request(): void
    {
        $user = $this->employer();
        Http::fake();

        $this->actingAs($user)
            ->from(route('employer.payment', 'basic'))
            ->post(route('employer.payment.order'), [
                'package' => 'basic',
                'payment_method' => 'mpesa',
                'mpesa_phone' => 'not-a-phone',
            ])
            ->assertSessionHasErrors('mpesa_phone');

        $this->assertDatabaseCount('employer_payment_orders', 0);
        Http::assertNothingSent();
    }

    public function test_rejected_mpesa_charge_is_logged_with_safe_paystack_diagnostics(): void
    {
        $user = $this->employer();
        Log::spy();
        Http::fake([
            'api.paystack.co/charge' => Http::response([
                'status' => false,
                'message' => 'M-Pesa is not available for this test account.',
                'code' => 'channel_unavailable',
                'data' => ['status' => 'failed'],
            ], 422),
        ]);

        $response = $this->actingAs($user)->post(route('employer.payment.order'), [
            'package' => 'basic',
            'payment_method' => 'mpesa',
            'mpesa_phone' => '0712 345 678',
        ]);

        $order = EmployerPaymentOrder::query()->firstOrFail();
        $response->assertRedirect(route('employer.payment.pending', $order->order_reference));
        $response->assertSessionHas('payment_error');
        $this->assertSame('pending', $order->status);
        $this->assertSame('initialization_failed', $order->paystack_status);
        $this->assertNull($order->paystack_reference);
        $this->assertSame(0, $user->employerSubscriptions()->count());

        Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'Paystack payment initialization failed.'
                    && $context['payment_method'] === 'mpesa'
                    && $context['failure_type'] === 'http_error'
                    && $context['http_status'] === 422
                    && $context['paystack_status'] === false
                    && $context['paystack_message'] === 'M-Pesa is not available for this test account.'
                    && $context['gateway_status'] === 'failed'
                    && $context['diagnostic_code'] === 'channel_unavailable';
            });
    }

    public function test_mpesa_timeout_leaves_the_order_pending_and_preserves_the_reference_for_reconciliation(): void
    {
        $user = $this->employer();
        Http::fake([
            'api.paystack.co/charge' => Http::failedConnection('cURL error 28: Operation timed out'),
        ]);

        $response = $this->actingAs($user)->post(route('employer.payment.order'), [
            'package' => 'basic',
            'payment_method' => 'mpesa',
            'mpesa_phone' => '0712 345 678',
        ]);

        $order = EmployerPaymentOrder::query()->firstOrFail();
        $response->assertRedirect(route('employer.payment.pending', $order->order_reference));
        $response->assertSessionHas('payment_error', 'Paystack did not respond in time. Your payment was not started; please retry shortly.');
        $this->assertSame('pending', $order->status);
        $this->assertSame('initialization_failed', $order->paystack_status);
        $this->assertNotNull($order->paystack_reference);
        $this->assertSame(0, $user->employerSubscriptions()->count());
    }

    public function test_malformed_mpesa_response_does_not_create_a_pending_charge(): void
    {
        $user = $this->employer();
        Http::fake(function (ClientRequest $request) {
            return Http::response([
                'status' => true,
                'message' => 'Charge attempted',
                'data' => ['reference' => $request['reference']],
            ]);
        });

        $response = $this->actingAs($user)->post(route('employer.payment.order'), [
            'package' => 'basic',
            'payment_method' => 'mpesa',
            'mpesa_phone' => '0712 345 678',
        ]);

        $order = EmployerPaymentOrder::query()->firstOrFail();
        $response->assertRedirect(route('employer.payment.pending', $order->order_reference));
        $response->assertSessionHas('payment_error', 'Paystack returned an unexpected response. Your payment was not started; please retry shortly.');
        $this->assertSame('pending', $order->status);
        $this->assertSame('initialization_failed', $order->paystack_status);
        $this->assertNull($order->paystack_reference);
        $this->assertSame(0, $user->employerSubscriptions()->count());
    }

    public function test_mpesa_pending_response_keeps_the_order_unpaid(): void
    {
        $user = $this->employer();
        Http::fake(function (ClientRequest $request) {
            return Http::response([
                'status' => true,
                'message' => 'Charge attempted',
                'data' => [
                    'reference' => $request['reference'],
                    'status' => 'pending',
                    'display_text' => 'Your M-Pesa authorization is still pending.',
                ],
            ]);
        });

        $response = $this->actingAs($user)->post(route('employer.payment.order'), [
            'package' => 'basic',
            'payment_method' => 'mpesa',
            'mpesa_phone' => '0712 345 678',
        ]);

        $order = EmployerPaymentOrder::query()->firstOrFail();
        $response->assertRedirect(route('employer.payment.pending', $order->order_reference));
        $this->assertSame('pending', $order->status);
        $this->assertSame('pending', $order->paystack_status);
        $this->assertNotNull($order->paystack_reference);
        $this->assertSame('Your M-Pesa authorization is still pending.', $order->paystack_display_text);
        $this->assertSame(0, $user->employerSubscriptions()->count());
    }

    public function test_failed_paystack_initialization_leaves_order_pending_for_retry(): void
    {
        $user = $this->employer();
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => false,
                'message' => 'The request could not be completed.',
                'data' => null,
            ], 422),
        ]);

        $response = $this->actingAs($user)->post(route('employer.payment.order'), [
            'package' => 'starter',
            'payment_method' => 'card',
        ]);

        $order = EmployerPaymentOrder::query()->firstOrFail();
        $response->assertRedirect(route('employer.payment.pending', $order->order_reference));
        $response->assertSessionHas('payment_error');
        $this->assertSame('pending', $order->status);
        $this->assertNull($order->paystack_reference);
        $this->assertSame(0, $user->employerSubscriptions()->count());
    }

    public function test_callback_verifies_amount_currency_and_reference_before_activation(): void
    {
        $cases = [
            ['amount' => 1, 'currency' => 'KES', 'reference' => ''],
            ['amount' => 300000, 'currency' => 'USD', 'reference' => ''],
            ['amount' => 300000, 'currency' => 'KES', 'reference' => 'wrong-reference'],
        ];

        foreach ($cases as $case) {
            [$user, $order] = $this->pendingCardOrder();
            $case['reference'] = $case['reference'] ?: $order->paystack_reference;
            Http::fake([
                'api.paystack.co/transaction/verify/*' => Http::response([
                    'status' => true,
                    'data' => [
                        'status' => 'success',
                        'reference' => $case['reference'],
                        'amount' => $case['amount'],
                        'currency' => $case['currency'],
                        'paid_at' => now()->toIso8601String(),
                    ],
                ]),
            ]);

            $this->actingAs($user)
                ->get(route('paystack.callback', ['reference' => $order->paystack_reference]))
                ->assertRedirect(route('employer.payment.pending', $order->order_reference))
                ->assertSessionHas('payment_error');

            $this->assertSame(0, $user->employerSubscriptions()->count());
            $this->assertSame('pending', $order->refresh()->status);
        }
    }

    public function test_verified_callback_activates_once_and_later_webhook_is_idempotent(): void
    {
        [$user, $order] = $this->pendingCardOrder();
        Http::fake(['api.paystack.co/transaction/verify/*' => $this->successfulVerification($order)]);

        $this->actingAs($user)
            ->get(route('paystack.callback', ['reference' => $order->paystack_reference]))
            ->assertRedirect(route('employer.payment.pending', $order->order_reference))
            ->assertSessionHas('payment_success');

        $this->assertSame(1, $user->employerSubscriptions()->count());
        $this->assertSame('paid', $order->refresh()->status);

        $this->sendWebhook($order->paystack_reference);

        $this->assertSame(1, $user->employerSubscriptions()->count());
        Http::assertSentCount(1);
    }

    public function test_webhook_can_confirm_before_callback_and_duplicate_webhook_does_not_duplicate_subscription(): void
    {
        [$user, $order] = $this->pendingCardOrder();
        Http::fake(['api.paystack.co/transaction/verify/*' => $this->successfulVerification($order)]);

        $this->sendWebhook($order->paystack_reference)->assertOk();
        $this->sendWebhook($order->paystack_reference)->assertOk();
        $this->actingAs($user)
            ->get(route('paystack.callback', ['reference' => $order->paystack_reference]))
            ->assertRedirect(route('employer.payment.pending', $order->order_reference));

        $this->assertSame(1, $user->employerSubscriptions()->count());
        $this->assertSame('paid', $order->refresh()->status);
        Http::assertSentCount(1);
    }

    public function test_invalid_webhook_signature_is_rejected_without_gateway_verification(): void
    {
        Http::fake();
        $raw = json_encode(['event' => 'charge.success', 'data' => ['reference' => 'unknown-ref']], JSON_THROW_ON_ERROR);

        $response = $this->call('POST', route('paystack.webhook'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_PAYSTACK_SIGNATURE' => 'invalid-signature',
        ], $raw);

        $response->assertUnauthorized();
        Http::assertNothingSent();
    }

    public function test_callback_cannot_access_another_employers_payment_order(): void
    {
        [$owner, $order] = $this->pendingCardOrder();
        $otherEmployer = $this->employer();
        Http::fake();

        $this->actingAs($otherEmployer)
            ->get(route('paystack.callback', ['reference' => $order->paystack_reference]))
            ->assertNotFound();

        $this->assertSame(0, $owner->employerSubscriptions()->count());
        Http::assertNothingSent();
    }

    public function test_pending_verification_never_activates_a_subscription(): void
    {
        [$user, $order] = $this->pendingCardOrder();
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => [
                    'status' => 'pending',
                    'reference' => $order->paystack_reference,
                    'amount' => $order->amount * 100,
                    'currency' => 'KES',
                ],
            ]),
        ]);

        $this->actingAs($user)
            ->get(route('paystack.callback', ['reference' => $order->paystack_reference]))
            ->assertRedirect(route('employer.payment.pending', $order->order_reference))
            ->assertSessionHas('payment_pending');

        $this->assertSame(0, $user->employerSubscriptions()->count());
        $this->assertSame('pending', $order->refresh()->status);
    }

    private function employer(): User
    {
        $user = User::query()->create([
            'name' => 'Paystack Test Employer',
            'email' => 'paystack-' . Str::uuid() . '@example.test',
            'password' => Hash::make('test-password'),
            'account_type' => 'employer',
        ]);
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }

    private function pendingCardOrder(): array
    {
        $user = $this->employer();
        $reference = strtolower(Str::random(32));
        $order = EmployerPaymentOrder::query()->create([
            'user_id' => $user->id,
            'order_reference' => (string) Str::uuid(),
            'plan' => 'basic',
            'amount' => 3000,
            'job_allowance' => 3,
            'duration_unit' => 'days',
            'duration_value' => 30,
            'currency' => 'KES',
            'payment_method' => 'card',
            'status' => 'pending',
            'expires_at' => now()->addDay(),
            'paystack_reference' => $reference,
            'paystack_initiated_at' => now(),
        ]);

        return [$user, $order];
    }

    private function successfulVerification(EmployerPaymentOrder $order): array
    {
        return Http::response([
            'status' => true,
            'data' => [
                'status' => 'success',
                'reference' => $order->paystack_reference,
                'amount' => $order->amount * 100,
                'currency' => $order->currency,
                'paid_at' => now()->toIso8601String(),
            ],
        ]);
    }

    private function sendWebhook(string $reference)
    {
        $raw = json_encode([
            'event' => 'charge.success',
            'data' => ['reference' => $reference],
        ], JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha512', $raw, 'sk_test_fake_only');

        return $this->call('POST', route('paystack.webhook'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_PAYSTACK_SIGNATURE' => $signature,
        ], $raw);
    }
}
