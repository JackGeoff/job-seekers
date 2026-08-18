@extends('layouts.auth')

@section('content')
    <div class="animate-fade-in" x-data="{ showPassword: false, isSubmitting: false }">
        <div class="mb-8 sm:mb-10">
            <p class="mb-3 text-sm font-semibold text-indigo-600">WELCOME BACK</p>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Sign in to your account</h1>
            <p class="mt-3 text-base leading-7 text-slate-600">Pick up where you left off and keep your next opportunity moving.</p>
        </div>

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5" @submit="isSubmitting = true">
            @csrf

            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-slate-800">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="email"
                    class="block h-14 w-full rounded-xl border bg-white px-4 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/10 @else border-slate-200 @enderror">
                @error('email')
                    <p class="mt-2 flex items-center gap-1.5 text-sm text-red-600"><span aria-hidden="true">●</span>{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between gap-4">
                    <label for="password" class="text-sm font-medium text-slate-800">Password</label>
                    <a href="{{ route('password.request') }}" class="min-h-11 -my-2 inline-flex items-center text-sm font-medium text-indigo-600 transition hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Forgot password?</a>
                </div>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password"
                        class="block h-14 w-full rounded-xl border bg-white py-0 pl-4 pr-16 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 @error('password') border-red-500 focus:border-red-500 focus:ring-red-500/10 @else border-slate-200 @enderror">
                    <button type="button" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'" :aria-pressed="showPassword.toString()" class="absolute right-2 top-1/2 inline-flex h-10 -translate-y-1/2 items-center rounded-lg px-3 text-sm font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span x-text="showPassword ? 'Hide' : 'Show'"></span>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 flex items-center gap-1.5 text-sm text-red-600"><span aria-hidden="true">●</span>{{ $message }}</p>
                @enderror
            </div>

            <label class="flex min-h-11 w-fit cursor-pointer items-center gap-3 text-sm text-slate-600">
                <input type="checkbox" id="remember" name="remember" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span>Remember me for 30 days</span>
            </label>

            <button type="submit" :disabled="isSubmitting" class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-base font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 hover:shadow-indigo-600/30 focus:outline-none focus:ring-4 focus:ring-indigo-500/25 disabled:cursor-not-allowed disabled:bg-indigo-400 disabled:shadow-none">
                <svg x-show="isSubmitting" x-cloak class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                <span x-text="isSubmitting ? 'Signing in…' : 'Sign in'"></span>
            </button>
        </form>

        <div class="my-8 flex items-center gap-4" aria-hidden="true"><div class="h-px flex-1 bg-slate-200"></div><span class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">or</span><div class="h-px flex-1 bg-slate-200"></div></div>
        <p class="text-center text-sm text-slate-600">Don’t have an account? <a href="{{ route('register') }}" class="font-semibold text-indigo-600 transition hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Create account</a></p>
    </div>
@endsection
