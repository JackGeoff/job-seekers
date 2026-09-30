<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmployerOnboardingController extends Controller
{
    public const PACKAGES = [
        'basic' => [
            'name' => 'Basic',
            'price' => 'KES 3,000',
            'amount' => 3000,
            'job_allowance' => 1,
            'duration' => 'days:30',
            'jobs' => '1 Job',
            'validity' => '30 Days',
            'best_for' => 'Occasional hiring',
            'features' => [
                '1 job posting',
                '30-day visibility',
                'Candidate applications',
                'Employer profile',
                'Application management',
                'Email application notifications',
                'Job editing',
            ],
        ],

        'starter' => [
            'name' => 'Starter',
            'price' => 'KES 10,000',
            'amount' => 10000,
            'job_allowance' => 5,
            'duration' => 'days:60',
            'jobs' => '5 Jobs',
            'validity' => '60 Days',
            'best_for' => 'Small businesses',
            'features' => [
                '5 job postings',
                '30-day visibility per job',
                'Employer profile',
                'Candidate application management',
                'Email notifications',
                'Job editing',
                'Basic employer branding',
                '1 Featured Job',
                '1 Social Media Promotion',
            ],
        ],

        'business' => [
            'name' => 'Business',
            'price' => 'KES 35,000',
            'amount' => 35000,
            'job_allowance' => 25,
            'duration' => 'months:6',
            'jobs' => '25 Jobs',
            'validity' => '6 Months',
            'best_for' => 'Active recruiters',
            'features' => [
                '25 job postings',
                '30-day visibility per job',
                'Enhanced company profile',
                'Candidate application management',
                'Employer branding',
                '5 Featured Jobs',
                '3 Homepage Features',
                '5 Social Media Promotions',
                'Priority support',
                'Basic recruitment consultation',
                'Vacancy optimization',
            ],
        ],

        'enterprise' => [
            'name' => 'Enterprise',
            'price' => 'Custom',
            'jobs' => '50+ Jobs',
            'validity' => '12 Months / Contract',
            'best_for' => 'Large organisations',
            'features' => [
                'Large-volume job posting',
                'Dedicated employer account',
                'Enhanced company profile',
                'Priority vacancy placement',
                'Featured vacancies',
                'Social media promotion',
                'Recruitment support',
                'Shortlisting support',
                'Screening services',
                'Dedicated account management',
                'Custom terms and reporting',
            ],
        ],
    ];

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
            'packages' => self::PACKAGES,
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

        if (
            !is_string($packageKey)
            || !isset(self::PACKAGES[$packageKey])
            || $packageKey === 'enterprise'
        ) {
            return back()->withErrors([
                'package' => 'Please choose one of the available paid plans.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Store Selected Package
        |--------------------------------------------------------------------------
        |
        | This works for both new subscriptions and upgrades.
        | The existing subscription is not changed here.
        |
        */

        $request->session()->put(
            'employer.selected_package',
            $packageKey
        );

        return redirect()->route('employer.payment');
    }

    /*
    |--------------------------------------------------------------------------
    | Payment
    |--------------------------------------------------------------------------
    */

    public function payment(Request $request)
    {
        $this->ensureEmployer($request);

        $packageKey = session('employer.selected_package');

        if (
            !is_string($packageKey)
            || !isset(self::PACKAGES[$packageKey])
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
            'package' => self::PACKAGES[$packageKey],
            'packageKey' => $packageKey,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Complete Payment
    |--------------------------------------------------------------------------
    */

    public function completePayment(Request $request)
    {
        $this->ensureEmployer($request);

        $packageKey = session('employer.selected_package');

        if (
            !is_string($packageKey)
            || !isset(self::PACKAGES[$packageKey])
            || $packageKey === 'enterprise'
        ) {
            return redirect()
                ->route('employer.pricing')
                ->with(
                    'error',
                    'Choose a valid paid plan before checking out.'
                );
        }

        $method = $request->input('payment_method');

        if (!in_array($method, ['mpesa', 'card'], true)) {
            return back()->withErrors([
                'payment_method' => 'Choose M-Pesa or Card.',
            ]);
        }

        $data = $request->all();

        /*
        |--------------------------------------------------------------------------
        | M-Pesa Validation
        |--------------------------------------------------------------------------
        */

        if ($method === 'mpesa') {
            $data['mpesa_phone'] = preg_replace(
                '/\s+/',
                '',
                (string) $request->input('mpesa_phone')
            );

            $validator = Validator::make(
                $data,
                [
                    'mpesa_phone' => [
                        'required',
                        'regex:/^(?:254|0)[17][0-9]{8}$/',
                    ],
                ]
            );
        } else {
            /*
            |--------------------------------------------------------------------------
            | Card Validation
            |--------------------------------------------------------------------------
            */

            $validator = Validator::make(
                $data,
                [
                    'cardholder_name' => [
                        'required',
                        'string',
                        'max:255',
                    ],

                    'card_number' => [
                        'required',
                        'regex:/^[0-9 ]{12,23}$/',
                    ],

                    'card_expiry' => [
                        'required',
                        'regex:/^(0[1-9]|1[0-2])\/[0-9]{2}$/',
                    ],

                    'card_cvv' => [
                        'required',
                        'regex:/^[0-9]{3,4}$/',
                    ],
                ]
            );
        }

        if ($validator->fails()) {
            /*
            |--------------------------------------------------------------------------
            | Do Not Flash Payment Input
            |--------------------------------------------------------------------------
            |
            | This prevents payment/card fields from being stored in
            | the session.
            |
            */

            return back()->withErrors($validator);
        }

        $package = self::PACKAGES[$packageKey];

        $now = now();

        [$unit, $value] = explode(
            ':',
            $package['duration']
        );

        $expiresAt = $unit === 'months'
            ? $now->copy()->addMonths((int) $value)
            : $now->copy()->addDays((int) $value);

        /*
        |--------------------------------------------------------------------------
        | Create New Subscription
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | We create a NEW subscription.
        |
        | We do not modify or delete the employer's existing subscription.
        | This means an upgrade only takes effect after payment succeeds.
        |
        */

        DB::transaction(function () use (
            $request,
            $packageKey,
            $package,
            $method,
            $now,
            $expiresAt
        ) {
            $request->user()
                ->employerSubscriptions()
                ->create([
                    'plan' => $packageKey,
                    'amount' => $package['amount'],
                    'payment_method' => $method,
                    'status' => 'successful',
                    'transaction_reference' => 'DEMO-' . Str::upper(
                        Str::random(10)
                    ),
                    'paid_at' => $now,
                    'starts_at' => $now,
                    'expires_at' => $expiresAt,
                    'job_allowance' => $package['job_allowance'],
                ]);
        });

        /*
        |--------------------------------------------------------------------------
        | Clear Selected Package
        |--------------------------------------------------------------------------
        */

        $request->session()->forget(
            'employer.selected_package'
        );

        /*
        |--------------------------------------------------------------------------
        | Continue To Employer Profile
        |--------------------------------------------------------------------------
        |
        | The profile controller/dashboard logic can determine whether
        | the employer still needs to complete their profile.
        |
        */

        return redirect()
            ->route('employer.profile')
            ->with(
                'success',
                'Payment successful. Your new plan is now active.'
            );
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

