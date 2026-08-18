@extends('layouts.auth')

@section('content')
    <div class="animate-fade-in" x-data="{ isSubmitting: false }">
        <div class="mb-8 sm:mb-10">
            <p class="mb-3 text-sm font-semibold text-indigo-600">ACCOUNT RECOVERY</p>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Forgot your password?</h1>
            <p class="mt-3 text-base leading-7 text-slate-600">Enter your email address and we’ll send you a secure password reset link.</p>
        </div>
        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4" role="status" aria-live="polite"><div class="flex gap-3"><span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-600 text-sm font-bold text-white">✓</span><div><p class="text-sm font-semibold text-emerald-900">Check your inbox</p><p class="mt-1 text-sm leading-5 text-emerald-800">{{ session('status') }}</p></div></div></div>
        @endif
        <form method="POST" action="{{ route('password.email') }}" class="space-y-6" @submit="isSubmitting = true">
            @csrf
            <div><label for="email" class="mb-2 block text-sm font-medium text-slate-800">Email address</label><input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="email" class="block h-14 w-full rounded-xl border bg-white px-4 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/10 @else border-slate-200 @enderror">@error('email')<p class="mt-2 flex items-center gap-1.5 text-sm text-red-600"><span aria-hidden="true">●</span>{{ $message }}</p>@enderror</div>
            <button type="submit" :disabled="isSubmitting" class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-base font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 hover:shadow-indigo-600/30 focus:outline-none focus:ring-4 focus:ring-indigo-500/25 disabled:cursor-not-allowed disabled:bg-indigo-400 disabled:shadow-none"><svg x-show="isSubmitting" x-cloak class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span x-text="isSubmitting ? 'Sending link…' : 'Send reset link'"></span></button>
        </form>
        <a href="{{ route('login') }}" class="mt-7 inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-indigo-600 transition hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"><span aria-hidden="true">←</span> Back to login</a>
        <p class="mt-8 rounded-xl bg-slate-100 px-4 py-3 text-sm leading-6 text-slate-600">For your security, reset links expire after a short time. Remember to check your spam folder too.</p>
    </div>
@endsection
