<?php

use App\Http\Controllers\CandidateApplicationController;
use App\Http\Controllers\CandidateApplicationsController;
use App\Http\Controllers\CandidateJobController;
use App\Http\Controllers\CandidateProfileController;
use App\Http\Controllers\EmployerApplicationController;
use App\Http\Controllers\EmployerJobController;
use App\Http\Controllers\EmployerProfileController;
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

Route::get('/jobs/{job}/apply', [
    CandidateApplicationController::class,
    'create',
])->name('candidate.jobs.apply.create');


/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

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
        | Email Verification Check
        |--------------------------------------------------------------------------
        */

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        /*
        |--------------------------------------------------------------------------
        | Account Type Redirect
        |--------------------------------------------------------------------------
        */

        if ($user->account_type === 'candidate') {
            return redirect()->route('candidate.dashboard');
        }

        if ($user->account_type === 'employer') {
            return redirect()->route('employer.dashboard');
        }

        Auth::logout();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'Invalid account type.',
            ]);

    })->name('login.store');


    /*
    |--------------------------------------------------------------------------
    | Registration Page
    |--------------------------------------------------------------------------
    */

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');


    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */

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
                'max:30',
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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'account_type' => $validated['account_type'],
        ]);

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
        | Redirect To Verification Page
        |--------------------------------------------------------------------------
        */

        return redirect()->route('verification.notice');

    })->name('register.store');


    /*
    |--------------------------------------------------------------------------
    | Forgot Password Page
    |--------------------------------------------------------------------------
    */

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');


    /*
    |--------------------------------------------------------------------------
    | Send Password Reset Link
    |--------------------------------------------------------------------------
    */

    Route::post('/forgot-password', function (Request $request) {

        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        /*
        |--------------------------------------------------------------------------
        | Always Use A Generic Response
        |--------------------------------------------------------------------------
        */

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'status',
                'If an account exists with that email address, a password reset link has been sent.'
            );
        }

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

        if ($user->account_type === 'candidate') {
            return redirect()->route('candidate.profile');
        }

        if ($user->account_type === 'employer') {
            return redirect()->route('employer.profile');
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
    | Verified User Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('verified')->group(function () {

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


        /*
        |--------------------------------------------------------------------------
        | Candidate Dashboard / Jobs
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
        | Job Marketplace
        |--------------------------------------------------------------------------
        */

        /*
         * Show dedicated application form.
         */
        Route::post('/jobs/{job}/apply', [
            CandidateApplicationController::class,
            'store',
        ])->name('candidate.jobs.apply');


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
         * Secure CV download.
         *
         * The controller checks that the authenticated employer
         * owns the job associated with the application.
         */
        Route::get('/employer/applications/{application}/cv', [
            CandidateApplicationController::class,
            'downloadCv',
        ])->name('employer.applications.cv');


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
                return redirect()->route('employer.dashboard');
            }

            abort(403);

        })->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Employer Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/employer/dashboard', function () {

            $user = Auth::user();

            $employerProfile = $user->employerProfile;

            $jobs = $employerProfile?->jobs()->get() ?? collect();

            $jobIds = $jobs->pluck('id');

            $applicationQuery = Application::whereIn(
                'job_id',
                $jobIds
            );

            return view('dashboard.employer', [
                'activeJobCount' => $jobs
                    ->where('status', 'published')
                    ->count(),

                'applicationCount' => $applicationQuery->count(),

                'candidateCount' => $applicationQuery
                    ->distinct('candidate_profile_id')
                    ->count('candidate_profile_id'),
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
