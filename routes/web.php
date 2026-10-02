<?php

use App\Http\Controllers\CandidateApplicationController;
use App\Http\Controllers\CandidateApplicationsController;
use App\Http\Controllers\CandidateJobController;
use App\Http\Controllers\CandidateProfileController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\EmployerApplicationController;
use App\Http\Controllers\EmployerJobController;
use App\Http\Controllers\EmployerOnboardingController;
use App\Http\Controllers\EmployerProfileController;
use App\Http\Controllers\EmployerRegistrationController;
use App\Http\Controllers\JobController;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $jobs = Job::with('employerProfile')
        ->publiclyVisible()
        ->latest()
        ->take(6)
        ->get();

    return view('welcome', [
        'jobs' => $jobs,
    ]);
})->name('home');


Route::get('/jobs', [
    JobController::class,
    'index',
])->name('jobs.index');


Route::get('/jobs/{job}', [
    JobController::class,
    'show',
])->name('jobs.show');

Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/terms', 'legal.terms')->name('terms');


/*
|--------------------------------------------------------------------------
| Apply Route
|--------------------------------------------------------------------------
|
| This route intentionally remains public.
|
| A guest can click Apply.
| CandidateApplicationController will send them to registration.
|
*/

Route::get('/jobs/{job}/apply', [
    CandidateApplicationController::class,
    'create',
])->name('candidate.jobs.apply.create');


Route::get('/career-guide', [
    ContentController::class,
    'careerGuide',
])->name('career-guide');


Route::get('/pricing', [
    ContentController::class,
    'pricing',
])->name('pricing');


Route::get('/blog', [
    ContentController::class,
    'blog',
])->name('blog.index');


Route::get('/blog/{slug}', [
    ContentController::class,
    'article',
])->name('blog.show');


/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Employer Registration
    |--------------------------------------------------------------------------
    */

    Route::get('/employer/register', [
        EmployerRegistrationController::class,
        'create',
    ])->name('employer.register');


    Route::post('/employer/register', [
        EmployerRegistrationController::class,
        'store',
    ])->name('employer.register.store');


    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');


    Route::post('/login', function (Request $request) {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);


        if (!Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            return back()
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])
                ->onlyInput('email');
        }


        $request->session()->regenerate();

        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | Candidate Login
        |--------------------------------------------------------------------------
        |
        | Candidates can continue before email verification.
        | They will see a verification reminder on their dashboard.
        |
        */

        if ($user->account_type === 'candidate') {

            /*
            |--------------------------------------------------------------------------
            | If candidate needs to complete their profile
            |--------------------------------------------------------------------------
            */

            if (!$user->candidateProfile) {
                return redirect()->intended(
                    route('candidate.profile')
                );
            }

            return redirect()->intended(
                route('candidate.dashboard')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Employer Login
        |--------------------------------------------------------------------------
        |
        | Employer verification flow remains unchanged.
        |
        */

        if ($user->account_type === 'employer') {

            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }


            return redirect()->intended(
                route($user->hasCompletedEmployerProfile()
                    ? 'employer.dashboard'
                    : 'employer.pricing')
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Invalid Account Type
        |--------------------------------------------------------------------------
        */

        Auth::logout();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Invalid account type.',
            ]);

    })->name('login.store');


    /*
    |--------------------------------------------------------------------------
    | Candidate Registration
    |--------------------------------------------------------------------------
    */

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');


    Route::post('/register', function (Request $request) {

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'max:30',
            ],

            'location' => [
                'required_if:account_type,candidate',
                'nullable',
                'string',
                'max:255',
            ],

            'job_title' => [
                'required_if:account_type,candidate',
                'nullable',
                'string',
                'max:255',
            ],

            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],

            'account_type' => [
                'required',
                'in:candidate,employer',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'account_type' => $validated['account_type'],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create Candidate Profile
        |--------------------------------------------------------------------------
        */

        if ($user->account_type === 'candidate') {

            $user->candidateProfile()->create([
                'full_name' => $validated['name'],
                'phone' => $validated['phone'],
                'location' => $validated['location'] ?? null,
                'job_title' => $validated['job_title'] ?? null,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Log User In
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Send Email Verification
        |--------------------------------------------------------------------------
        */

        event(new Registered($user));


        /*
        |--------------------------------------------------------------------------
        | Candidate Registration
        |--------------------------------------------------------------------------
        |
        | Candidates go directly to profile completion.
        | They do NOT have to verify email first.
        |
        */

        if ($user->account_type === 'candidate') {
            return redirect()->route('candidate.profile');
        }


        /*
        |--------------------------------------------------------------------------
        | Employer Registration
        |--------------------------------------------------------------------------
        |
        | Employers keep the existing verification flow.
        |
        */

        return redirect()->route('verification.notice');

    })->name('register.store');


    /*
    |--------------------------------------------------------------------------
    | Forgot Password
    |--------------------------------------------------------------------------
    */

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');


    Route::post('/forgot-password', function (Request $request) {

        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);


        Password::sendResetLink(
            $request->only('email')
        );


        return back()->with(
            'status',
            'If an account exists with that email address, a password reset link has been sent.'
        );

    })->name('password.email');


    /*
    |--------------------------------------------------------------------------
    | Reset Password Page
    |--------------------------------------------------------------------------
    */

    Route::get('/reset-password/{token}', function (
        Request $request,
        string $token
    ) {
        return view('auth.reset-password', [
            'request' => $request,
            'token' => $token,
        ]);
    })->name('password.reset');


    /*
    |--------------------------------------------------------------------------
    | Reset Password
    |--------------------------------------------------------------------------
    */

    Route::post('/reset-password', function (Request $request) {

        $validated = $request->validate([
            'token' => ['required'],

            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'confirmed',
                PasswordRule::defaults(),
            ],
        ]);


        $status = Password::reset(
            $validated,
            function ($user, $password) {

                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                Auth::login($user);
            }
        );


        if ($status === Password::PASSWORD_RESET) {

            return redirect()
                ->route('login')
                ->with(
                    'status',
                    'Your password has been reset successfully. You can now log in.'
                );
        }


        return back()
            ->withErrors([
                'email' => __($status),
            ])
            ->withInput(
                $request->only('email')
            );

    })->name('password.update');

});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    */

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');


    Route::get('/email/verify/{id}/{hash}', function (
        EmailVerificationRequest $request
    ) {

        $request->fulfill();

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Candidate Verification
        |--------------------------------------------------------------------------
        */

        if ($user->account_type === 'candidate') {

            if (session()->has('apply_job_id')) {
                return redirect()->route('candidate.profile');
            }

            return redirect()->route('candidate.dashboard');
        }


        /*
        |--------------------------------------------------------------------------
        | Employer Verification
        |--------------------------------------------------------------------------
        */

        if ($user->account_type === 'employer') {
            return redirect()->route('employer.pricing');
        }


        return redirect()->route('dashboard');

    })
        ->middleware('signed')
        ->name('verification.verify');


    Route::post('/email/verification-notification', function (
        Request $request
    ) {

        if ($request->user()->hasVerifiedEmail()) {

            return redirect()->route('dashboard');
        }


        $request->user()->sendEmailVerificationNotification();


        return back()->with(
            'status',
            'A new verification link has been sent to your email address.'
        );

    })
        ->middleware('throttle:6,1')
        ->name('verification.send');


    /*
    |--------------------------------------------------------------------------
    | Candidate Routes
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | These are authenticated but NOT email-verified.
    |
    | Candidates can:
    | - complete their profile
    | - upload CV
    | - apply for jobs
    | - view dashboard
    |
    | Their dashboard reminds them to verify their email.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Candidate Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/candidate/profile', [
        CandidateProfileController::class,
        'create',
    ])->name('candidate.profile');


    Route::post('/candidate/profile', [
        CandidateProfileController::class,
        'store',
    ])->name('candidate.profile.store');


    Route::get('/candidate/profile/cv', [
        CandidateProfileController::class,
        'viewCv',
    ])->name('candidate.profile.cv');


    /*
    |--------------------------------------------------------------------------
    | Candidate Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/candidate/dashboard', [
        CandidateJobController::class,
        'index',
    ])->name('candidate.dashboard');


    Route::get('/candidate/jobs', [
        CandidateJobController::class,
        'index',
    ])->name('candidate.jobs.index');


    /*
    |--------------------------------------------------------------------------
    | Candidate Application Tracking
    |--------------------------------------------------------------------------
    */

    Route::get('/candidate/applications', [
        CandidateApplicationsController::class,
        'index',
    ])->name('candidate.applications.index');


    /*
    |--------------------------------------------------------------------------
    | Candidate Application Form
    |--------------------------------------------------------------------------
    */

    Route::post('/jobs/{job}/apply', [
        CandidateApplicationController::class,
        'store',
    ])->name('candidate.jobs.apply');


    /*
    |--------------------------------------------------------------------------
    | Candidate Application GET
    |--------------------------------------------------------------------------
    |
    | This route is protected by auth.
    | The public Apply route above handles guests.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function () {

        $user = Auth::user();


        if ($user->account_type === 'candidate') {
            return redirect()->route('candidate.dashboard');
        }


        if ($user->account_type === 'employer') {

            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }


            return redirect()->intended(
                route($user->hasCompletedEmployerProfile()
                    ? 'employer.dashboard'
                    : 'employer.pricing')
            );
        }


        abort(403);

    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Employer Routes
    |--------------------------------------------------------------------------
    |
    | Employers remain behind email verification.
    |
    */

    Route::middleware('verified')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Employer Pricing
        |--------------------------------------------------------------------------
        */

        Route::get('/employer/pricing', [
            EmployerOnboardingController::class,
            'pricing',
        ])->name('employer.pricing');


        Route::post('/employer/pricing', [
            EmployerOnboardingController::class,
            'selectPlan',
        ])->name('employer.pricing.select');


        /*
        |--------------------------------------------------------------------------
        | Employer Payment
        |--------------------------------------------------------------------------
        */

        Route::get('/employer/payment/{package?}', [
            EmployerOnboardingController::class,
            'payment',
        ])->name('employer.payment');


        Route::post('/employer/payment/orders', [
            EmployerOnboardingController::class,
            'createPaymentOrder',
        ])->name('employer.payment.order');


        Route::get('/employer/payment/orders/{orderReference}', [
            EmployerOnboardingController::class,
            'paymentPending',
        ])->name('employer.payment.pending');


        /*
        |--------------------------------------------------------------------------
        | Employer Enterprise
        |--------------------------------------------------------------------------
        */

        Route::get('/employer/enterprise', [
            EmployerOnboardingController::class,
            'enterprise',
        ])->name('employer.enterprise');


        /*
        |--------------------------------------------------------------------------
        | Employer Profile
        |--------------------------------------------------------------------------
        */

        Route::get('/employer/profile', [
            EmployerProfileController::class,
            'create',
        ])->name('employer.profile');


        Route::post('/employer/profile', [
            EmployerProfileController::class,
            'store',
        ])->name('employer.profile.store');


        /*
        |--------------------------------------------------------------------------
        | Employer Jobs
        |--------------------------------------------------------------------------
        */

        Route::get('/employer/jobs', [
            EmployerJobController::class,
            'index',
        ])->name('employer.jobs.index');


        Route::get('/employer/jobs/create', [
            EmployerJobController::class,
            'create',
        ])->name('employer.jobs.create');


        Route::post('/employer/jobs', [
            EmployerJobController::class,
            'store',
        ])->name('employer.jobs.store');


        Route::get('/employer/jobs/{job}/edit', [
            EmployerJobController::class,
            'edit',
        ])->name('employer.jobs.edit');


        Route::put('/employer/jobs/{job}', [
            EmployerJobController::class,
            'update',
        ])->name('employer.jobs.update');


        Route::patch('/employer/jobs/{job}/close', [
            EmployerJobController::class,
            'close',
        ])->name('employer.jobs.close');


        Route::delete('/employer/jobs/{job}', [
            EmployerJobController::class,
            'destroy',
        ])->name('employer.jobs.destroy');


        /*
        |--------------------------------------------------------------------------
        | Employer Applications
        |--------------------------------------------------------------------------
        */

        Route::get('/employer/applications', [
            EmployerApplicationController::class,
            'index',
        ])->name('employer.applications.index');


        /*
        |--------------------------------------------------------------------------
        | Secure CV Download
        |--------------------------------------------------------------------------
        */

        Route::get('/employer/applications/{application}/cv', [
            CandidateApplicationController::class,
            'downloadCv',
        ])->name('employer.applications.cv');


        /*
        |--------------------------------------------------------------------------
        | Employer Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/employer/dashboard', function () {

            $user = Auth::user();

            abort_unless(
                $user->account_type === 'employer',
                403
            );


            if (!$user->hasCompletedEmployerProfile()) {
                return redirect()->route('employer.profile');
            }


            $employerProfile = $user->employerProfile;

            $jobs = $employerProfile?->jobs()->get() ?? collect();

            $jobIds = $jobs->pluck('id');
            $activeJobCount = $employerProfile->jobs()
                ->publiclyVisible()
                ->count();


            $applicationQuery = Application::whereIn(
                'job_id',
                $jobIds
            );


            $activeSubscription = $user->activeEmployerSubscription();
            $subscription = $activeSubscription
                ?? $user->latestSuccessfulEmployerSubscription();
            $jobsUsed = $subscription
                ? $user->employerSubscriptionCreditsUsed($subscription)
                : 0;
            $jobsRemaining = $subscription
                ? max(0, $subscription->job_allowance - $jobsUsed)
                : 0;
            $isExpired = !$activeSubscription
                && $subscription?->expires_at !== null
                && $subscription->expires_at->isPast();
            $isExhausted = $subscription !== null
                && $jobsUsed >= $subscription->job_allowance;
            $subscriptionStatus = !$subscription
                ? 'none'
                : ($isExpired ? 'expired' : ($isExhausted ? 'exhausted' : 'active'));
            $subscriptionMessage = match (true) {
                !$subscription => 'Choose a subscription plan to start posting jobs.',
                $isExpired && $isExhausted => 'Your plan has expired and your posting limit has been reached. Renew or upgrade to continue posting jobs.',
                $isExpired => 'Your subscription has expired. Renew or upgrade your plan to continue posting jobs.',
                $isExhausted => 'Your job posting limit has been reached. Upgrade your plan to publish more jobs.',
                default => null,
            };

            return view('dashboard.employer', [

                'activeJobCount' => $activeJobCount,

                'applicationCount' => $applicationQuery->count(),

                'candidateCount' => $applicationQuery
                    ->distinct('candidate_profile_id')
                    ->count('candidate_profile_id'),

                'subscription' => $subscription,
                'subscriptionStatus' => $subscriptionStatus,
                'subscriptionMessage' => $subscriptionMessage,
                'jobsUsed' => $jobsUsed,
                'jobsRemaining' => $jobsRemaining,
                'usagePercentage' => $subscription && $subscription->job_allowance > 0
                    ? min(100, (int) round($jobsUsed / $subscription->job_allowance * 100))
                    : 0,
            ]);

        })->name('employer.dashboard');

    });


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', function (Request $request) {

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');

    })->name('logout');

});