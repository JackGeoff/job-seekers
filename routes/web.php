<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CandidateProfileController;
use App\Http\Controllers\EmployerProfileController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Login page
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    // Login
    Route::post('/login', function (\Illuminate\Http\Request $request) {

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $request->session()->regenerate();

            $user = Auth::user();

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
        }

        return back()
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])
            ->onlyInput('email');

    })->name('login.store');


    // Registration page
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');


    // Registration
    Route::post('/register', function (\Illuminate\Http\Request $request) {

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'confirmed', 'min:8'],
            'account_type' => ['required', 'in:candidate,employer'],
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'],
            'account_type' => $validated['account_type'],
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        if ($user->account_type === 'candidate') {
            return redirect()->route('candidate.profile');
        }

        if ($user->account_type === 'employer') {
            return redirect()->route('employer.profile');
        }

        return redirect()->route('dashboard');

    })->name('register.store');


    // Forgot password page
    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');


    // Forgot password request
    Route::post('/forgot-password', function (\Illuminate\Http\Request $request) {

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Password reset functionality will be implemented next.

        return back()->with(
            'status',
            'Password reset link sent!'
        );

    })->name('password.email');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Candidate Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/candidate/profile', [
        CandidateProfileController::class,
        'create'
    ])->name('candidate.profile');

    Route::post('/candidate/profile', [
        CandidateProfileController::class,
        'store'
    ])->name('candidate.profile.store');


    /*
    |--------------------------------------------------------------------------
    | Employer Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/employer/profile', [
        EmployerProfileController::class,
        'create'
    ])->name('employer.profile');

    Route::post('/employer/profile', [
        EmployerProfileController::class,
        'store'
    ])->name('employer.profile.store');


    /*
    |--------------------------------------------------------------------------
    | Dashboards
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


    // Candidate Dashboard
    Route::get('/candidate/dashboard', function () {
        return view('dashboard.candidate');
    })->name('candidate.dashboard');


    // Employer Dashboard
    Route::get('/employer/dashboard', function () {
        return view('dashboard.employer');
    })->name('employer.dashboard');


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', function (\Illuminate\Http\Request $request) {

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');

    })->name('logout');

});
