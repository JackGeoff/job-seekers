<?php

namespace App\Http\Controllers;

use App\Models\EmployerPaymentOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EmployerOnboardingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Employer Pricing
    |--------------------------------------------------------------------------
    |
    | This page is available to:
    |
    | 1. New employers who have not subscribed yet.
    | 2. Existing employers who want to upgrade or change their plan.
    |
    */

    public function pricing(Request $request)
    {
        $this->ensureEmployer($request);

        $enterpriseEnquiry = $request->boolean('enterprise');

        return view('employer.pricing', [
            'packages' => config('employer_plans', []),
            'enterpriseEnquiry' => $enterpriseEnquiry,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Select Plan
    |--------------------------------------------------------------------------
    */

    public function selectPlan(Request $request)
    {
        $this->ensureEmployer($request);

        $packageKey = $request->input('package');
        $packages = config('employer_plans', []);

        if (
            !is_string($packageKey)
            || !isset($packages[$packageKey])
            || $packageKey === 'enterprise'
        ) {
            return back()->withErrors([
                'package' => 'Please choose one of the available paid plans.',
            ]);
        }

        $request->session()->put('employer.selected_package', $packageKey);

        /*
        |--------------------------------------------------------------------------
        | Store Selected Package
        |--------------------------------------------------------------------------
        |
        | This works for both new subscriptions and upgrades.
        | The existing subscription is not changed here.
        |
        */

        return redirect()->route('employer.payment', $packageKey);
    }

    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    */

    public function payment(Request $request, ?string $package = null)
    {
        $this->ensureEmployer($request);

        $packageKey = $package ?? session('employer.selected_package');
        $packages = config('employer_plans', []);

        if (
            !is_string($packageKey)
            || !isset($packages[$packageKey])
            || $packageKey === 'enterprise'
        ) {
            return redirect()
                ->route('employer.pricing')
                ->with(
                    'error',
                    'Choose a paid plan before continuing to checkout.'
                );
        }

        return view('employer.payment', [
            'package' => $packages[$packageKey],
            'packageKey' => $packageKey,
            'account' => $request->user(),
            'companyProfile' => $request->user()->employerProfile,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Complete Payment
    |--------------------------------------------------------------------------
    */

    public function createPaymentOrder(Request $request)
    {
        $this->ensureEmployer($request);

        $packages = config('employer_plans', []);
        $packageKeys = array_keys(array_filter(
            $packages,
            fn (array $package): bool => $package['amount'] !== null
        ));

        $validated = $request->validate([
            'package' => ['required', 'string', Rule::in($packageKeys)],
            'payment_method' => ['required', Rule::in(['mpesa', 'card', 'bank_transfer'])],
        ]);

        $packageKey = $validated['package'];
        $package = $packages[$packageKey] ?? null;

        if (!$package || $package['amount'] === null) {
            return redirect()
                ->route('employer.pricing')
                ->with(
                    'error',
                    'Choose a valid standard plan before selecting a payment method.'
                );
        }

        $now = now();
        $order = DB::transaction(fn () => EmployerPaymentOrder::create([
            'user_id' => $request->user()->id,
            'order_reference' => (string) Str::uuid(),
            'plan' => $packageKey,
            'amount' => $package['amount'],
            'job_allowance' => $package['job_allowance'],
            'duration_unit' => $package['duration_unit'],
            'duration_value' => $package['duration_value'],
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'expires_at' => $now->copy()->addDay(),
        ]));

        return redirect()->route(
            'employer.payment.pending',
            $order->order_reference
        );
    }

    public function paymentPending(Request $request, string $orderReference)
    {
        $this->ensureEmployer($request);

        $order = EmployerPaymentOrder::query()
            ->where('user_id', $request->user()->id)
            ->where('order_reference', $orderReference)
            ->firstOrFail();

        return view('employer.payment-pending', [
            'order' => $order,
            'package' => config("employer_plans.{$order->plan}"),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Enterprise
    |--------------------------------------------------------------------------
    */

    public function enterprise(Request $request)
    {
        $this->ensureEmployer($request);

        return redirect()->route(
            'employer.pricing',
            [
                'enterprise' => 1,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ensure Employer
    |--------------------------------------------------------------------------
    */

    private function ensureEmployer(Request $request): void
    {
        abort_unless(
            $request->user()->account_type === 'employer',
            403
        );
    }
}

