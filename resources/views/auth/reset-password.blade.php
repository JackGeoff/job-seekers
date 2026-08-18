@extends('layouts.auth')

@section('content')
    <div class="animate-fade-in" x-data="{
        showPassword: false, showConfirmation: false, password: '', isSubmitting: false,
        strength() {
            if (!this.password.length) return { label: 'Start typing', width: 'w-0', color: 'bg-slate-200' };
            if (this.password.length < 8) return { label: 'Too short', width: 'w-1/3', color: 'bg-rose-500' };
            if (!/[A-Z]/.test(this.password) || !/\d/.test(this.password)) return { label: 'Good', width: 'w-2/3', color: 'bg-amber-500' };
            return { label: 'Strong', width: 'w-full', color: 'bg-emerald-500' };
        }
    }">
        <div class="mb-8 sm:mb-10">
            <p class="mb-3 text-sm font-semibold text-indigo-600">ACCOUNT RECOVERY</p>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Create a new password</h1>
            <p class="mt-3 text-base leading-7 text-slate-600">Enter your new password below.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4" role="status" aria-live="polite"><p class="text-sm font-medium text-emerald-800">{{ session('status') }}</p></div>
        @endif

        <form method="POST" action="{{ url()->current() }}" class="space-y-5" @submit="isSubmitting = true">
            @csrf
            {{-- Connect this form action to Laravel's password.update route when the reset backend is added. --}}
            @if (isset($token))<input type="hidden" name="token" value="{{ $token }}">@endif
            <div><label for="email" class="mb-2 block text-sm font-medium text-slate-800">Email address</label><input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" placeholder="you@example.com" required autocomplete="email" class="block h-14 w-full rounded-xl border bg-white px-4 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/10 @else border-slate-200 @enderror">@error('email')<p class="mt-2 flex items-center gap-1.5 text-sm text-red-600"><span aria-hidden="true">●</span>{{ $message }}</p>@enderror</div>
            <div><label for="password" class="mb-2 block text-sm font-medium text-slate-800">New password</label><div class="relative"><input :type="showPassword ? 'text' : 'password'" id="password" name="password" x-model="password" placeholder="Create a new password" required autocomplete="new-password" class="block h-14 w-full rounded-xl border bg-white py-0 pl-4 pr-16 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 @error('password') border-red-500 focus:border-red-500 focus:ring-red-500/10 @else border-slate-200 @enderror"><button type="button" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'" :aria-pressed="showPassword.toString()" class="absolute right-2 top-1/2 inline-flex h-10 -translate-y-1/2 items-center rounded-lg px-3 text-sm font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"><span x-text="showPassword ? 'Hide' : 'Show'"></span></button></div><div class="mt-3" aria-live="polite"><div class="flex h-1.5 overflow-hidden rounded-full bg-slate-100"><span class="h-full rounded-full transition-all duration-300" :class="[strength().width, strength().color]"></span></div><p class="mt-2 text-xs text-slate-500">Use 8+ characters with an uppercase letter and number. <span class="font-medium text-slate-700" x-text="strength().label"></span></p></div>@error('password')<p class="mt-2 flex items-center gap-1.5 text-sm text-red-600"><span aria-hidden="true">●</span>{{ $message }}</p>@enderror</div>
            <div><label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-800">Confirm new password</label><div class="relative"><input :type="showConfirmation ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" placeholder="Confirm your new password" required autocomplete="new-password" class="block h-14 w-full rounded-xl border bg-white py-0 pl-4 pr-16 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 @error('password_confirmation') border-red-500 focus:border-red-500 focus:ring-red-500/10 @else border-slate-200 @enderror"><button type="button" @click="showConfirmation = !showConfirmation" :aria-label="showConfirmation ? 'Hide password confirmation' : 'Show password confirmation'" :aria-pressed="showConfirmation.toString()" class="absolute right-2 top-1/2 inline-flex h-10 -translate-y-1/2 items-center rounded-lg px-3 text-sm font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"><span x-text="showConfirmation ? 'Hide' : 'Show'"></span></button></div>@error('password_confirmation')<p class="mt-2 flex items-center gap-1.5 text-sm text-red-600"><span aria-hidden="true">●</span>{{ $message }}</p>@enderror</div>
            <button type="submit" :disabled="isSubmitting" class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-base font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 hover:shadow-indigo-600/30 focus:outline-none focus:ring-4 focus:ring-indigo-500/25 disabled:cursor-not-allowed disabled:bg-indigo-400 disabled:shadow-none"><svg x-show="isSubmitting" x-cloak class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span x-text="isSubmitting ? 'Resetting password…' : 'Reset password'"></span></button>
        </form>
    </div>
@endsection
