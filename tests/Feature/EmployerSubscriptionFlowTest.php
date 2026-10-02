<?php

namespace Tests\Feature;

use App\Models\EmployerPaymentOrder;
use App\Models\EmployerProfile;
use App\Models\EmployerSubscription;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class EmployerSubscriptionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_uses_server_plan_price_and_creates_only_a_pending_order(): void
    {
        $user = $this->createEmployerUser();

        foreach (['mpesa', 'card', 'bank_transfer'] as $method) {
            $response = $this->actingAs($user)->post(route('employer.payment.order'), [
                'package' => 'growth',
                'payment_method' => $method,
                'amount' => 1,
                'job_allowance' => 999,
            ]);

            $order = EmployerPaymentOrder::query()
                ->where('payment_method', $method)
                ->firstOrFail();

            $response->assertRedirect(route('employer.payment.pending', $order->order_reference));
            $this->assertSame(20000, $order->amount);
            $this->assertSame(20, $order->job_allowance);
            $this->assertSame('pending', $order->status);

            $pendingPage = $this->get(route('employer.payment.pending', $order->order_reference));
            $pendingPage->assertOk()->assertSee('has not activated a subscription');

            if ($method === 'bank_transfer') {
                $pendingPage->assertSee('1003249278')->assertSee('manual verification');
            }
        }

        $this->assertDatabaseMissing('employer_subscriptions', ['user_id' => $user->id]);
    }

    public function test_enterprise_and_unknown_plans_cannot_create_standard_checkout_orders(): void
    {
        $user = $this->createEmployerUser();

        foreach (['enterprise', 'unknown'] as $package) {
            $this->actingAs($user)
                ->from(route('employer.pricing'))
                ->post(route('employer.payment.order'), [
                    'package' => $package,
                    'payment_method' => 'card',
                ])
                ->assertSessionHasErrors('package');
        }

        $this->assertDatabaseCount('employer_payment_orders', 0);
    }

    public function test_batch_posting_persists_each_selected_category_and_retries_idempotently(): void
    {
        [$user, $profile, $subscription] = $this->createEmployerWithSubscription();
        $jobs = [
            $this->jobPayload('Software Engineering & Development'),
            $this->jobPayload('Data Science, AI & Machine Learning'),
        ];
        $payload = ['jobs' => $jobs];

        $response = $this->actingAs($user)
            ->post(route('employer.jobs.store'), $payload);

        $response->assertRedirect(route('employer.jobs.index'));
        $this->assertSame(2, $profile->jobs()->count());
        $this->assertSame(2, $subscription->jobs()->count());
        $this->assertDatabaseHas('job_listings', [
            'title' => 'Test Software Role',
            'category' => 'Software Engineering & Development',
            'subscription_id' => $subscription->id,
            'status' => 'published',
        ]);
        $this->assertNotNull($profile->jobs()->where('title', 'Test Software Role')->firstOrFail()->application_deadline);

        $this->get(route('jobs.index', ['q' => 'Test Software Role']))
            ->assertOk()
            ->assertSee('Software Engineering & Development');

        $this->actingAs($user)
            ->post(route('employer.jobs.store'), $payload)
            ->assertRedirect(route('employer.jobs.index'));

        $this->assertSame(2, $profile->jobs()->count());
    }

    public function test_search_keyword_is_rejected_as_a_category(): void
    {
        [$user] = $this->createEmployerWithSubscription();
        $payload = $this->jobPayload('software');

        $this->actingAs($user)
            ->from(route('employer.jobs.create'))
            ->post(route('employer.jobs.store'), ['jobs' => [$payload]])
            ->assertSessionHasErrors('jobs.0.category');

        $this->assertDatabaseCount('job_listings', 0);
    }

    public function test_expired_subscription_cannot_publish_but_can_save_a_draft(): void
    {
        [$user, $profile] = $this->createEmployerWithSubscription(expiresAt: now()->subDay());
        $payload = $this->jobPayload('Software Engineering & Development');

        $this->actingAs($user)
            ->from(route('employer.jobs.create'))
            ->post(route('employer.jobs.store'), ['jobs' => [$payload]])
            ->assertSessionHasErrors('jobs');

        $payload['status'] = 'draft';
        $this->actingAs($user)
            ->post(route('employer.jobs.store'), ['jobs' => [$payload]])
            ->assertRedirect(route('employer.jobs.index'));

        $this->assertSame('draft', $profile->jobs()->firstOrFail()->status);
        $this->assertNull($profile->jobs()->firstOrFail()->subscription_id);
    }

    public function test_closing_a_published_job_does_not_restore_its_credit(): void
    {
        [$user, $profile, $subscription] = $this->createEmployerWithSubscription(allowance: 1);
        $firstJob = $this->jobPayload('Software Engineering & Development');

        $this->actingAs($user)
            ->post(route('employer.jobs.store'), ['jobs' => [$firstJob]])
            ->assertRedirect(route('employer.jobs.index'));

        $job = $profile->jobs()->firstOrFail();
        $this->patch(route('employer.jobs.close', $job))
            ->assertRedirect(route('employer.jobs.index'));

        $this->assertSame(1, $user->employerSubscriptionCreditsUsed($subscription));

        $secondJob = $this->jobPayload('Data Science, AI & Machine Learning');
        $this->from(route('employer.jobs.create'))
            ->post(route('employer.jobs.store'), ['jobs' => [$secondJob]])
            ->assertSessionHasErrors('jobs');

        $this->assertSame(1, $profile->jobs()->count());
    }

    private function createEmployerWithSubscription(
        ?\Illuminate\Support\Carbon $expiresAt = null,
        int $allowance = 4
    ): array
    {
        $user = $this->createEmployerUser();
        $profile = EmployerProfile::query()->create([
            'user_id' => $user->id,
            'company_name' => 'Test Employer',
            'industry' => 'Technology',
            'location' => 'Nairobi',
            'phone' => '0712345678',
        ]);
        $subscription = EmployerSubscription::query()->create([
            'user_id' => $user->id,
            'plan' => 'growth',
            'amount' => 20000,
            'payment_method' => 'bank_transfer',
            'status' => 'successful',
            'transaction_reference' => (string) Str::uuid(),
            'paid_at' => now()->subDay(),
            'starts_at' => now()->subDay(),
            'expires_at' => $expiresAt ?? now()->addMonths(3),
            'job_allowance' => $allowance,
        ]);

        return [$user, $profile, $subscription];
    }

    private function createEmployerUser(): User
    {
        $user = User::query()->create([
            'name' => 'Subscription Flow Test',
            'email' => 'subscription-flow-' . Str::uuid() . '@example.test',
            'password' => Hash::make('test-password'),
            'account_type' => 'employer',
        ]);
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }

    private function jobPayload(string $category): array
    {
        return [
            'submission_key' => (string) Str::uuid(),
            'title' => 'Test Software Role',
            'description' => 'This test role has enough meaningful text to pass the minimum description length and exercise batch posting safely.',
            'category' => $category,
            'location' => 'Nairobi',
            'employment_type' => 'full-time',
            'salary_currency' => 'KES',
            'status' => 'published',
        ];
    }
}