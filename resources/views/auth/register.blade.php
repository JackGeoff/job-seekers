@extends('layouts.auth')

@section('content')
    <div class="animate-fade-in" x-data="{
        showPassword: false,
        showConfirmation: false,
        password: '',
        accountType: '{{ old('account_type', 'candidate') }}',
        isSubmitting: false,
        strength() {
            if (!this.password.length) return { label: 'Start typing', width: 'w-0', color: 'bg-slate-200' };
            if (this.password.length < 8) return { label: 'Too short', width: 'w-1/3', color: 'bg-rose-500' };
            if (!/[A-Z]/.test(this.password) || !/\d/.test(this.password)) return { label: 'Good', width: 'w-2/3', color: 'bg-amber-500' };
            return { label: 'Strong', width: 'w-full', color: 'bg-emerald-500' };
        }
    }">
        <div class="mb-8 sm:mb-10">
            <p class="mb-3 text-sm font-semibold text-indigo-600">CREATE YOUR ACCOUNT</p>
            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Join the platform and get started.</h1>
            <p class="mt-3 text-base leading-7 text-slate-600">Create your profile to find a better match, faster.</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="space-y-5" @submit="isSubmitting = true">
            @csrf

            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-slate-800">Full name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Jane Wanjiku" required autofocus autocomplete="name"
                    class="block h-14 w-full rounded-xl border bg-white px-4 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 @error('name') border-red-500 focus:border-red-500 focus:ring-red-500/10 @else border-slate-200 @enderror">
                @error('name')<p class="mt-2 flex items-center gap-1.5 text-sm text-red-600"><span aria-hidden="true">●</span>{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="mb-2 block text-sm font-medium text-slate-800">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="jane@example.com" required autocomplete="email"
                    class="block h-14 w-full rounded-xl border bg-white px-4 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 @error('email') border-red-500 focus:border-red-500 focus:ring-red-500/10 @else border-slate-200 @enderror">
                @error('email')<p class="mt-2 flex items-center gap-1.5 text-sm text-red-600"><span aria-hidden="true">●</span>{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="phone" class="mb-2 block text-sm font-medium text-slate-800">Phone number</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+254 700 000 000" required autocomplete="tel"
                    class="block h-14 w-full rounded-xl border border-slate-200 bg-white px-4 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10">
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-slate-800">Password</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" id="password" name="password" x-model="password" placeholder="Create a password" required autocomplete="new-password"
                        class="block h-14 w-full rounded-xl border bg-white py-0 pl-4 pr-16 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 @error('password') border-red-500 focus:border-red-500 focus:ring-red-500/10 @else border-slate-200 @enderror">
                    <button type="button" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'" :aria-pressed="showPassword.toString()" class="absolute right-2 top-1/2 inline-flex h-10 -translate-y-1/2 items-center rounded-lg px-3 text-sm font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"><span x-text="showPassword ? 'Hide' : 'Show'"></span></button>
                </div>
                <div class="mt-3" aria-live="polite">
                    <div class="flex h-1.5 gap-1 overflow-hidden rounded-full bg-slate-100"><span class="h-full rounded-full transition-all duration-300" :class="[strength().width, strength().color]"></span></div>
                    <p class="mt-2 text-xs text-slate-500">Use 8+ characters with an uppercase letter and number. <span class="font-medium text-slate-700" x-text="strength().label"></span></p>
                </div>
                @error('password')<p class="mt-2 flex items-center gap-1.5 text-sm text-red-600"><span aria-hidden="true">●</span>{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-800">Confirm password</label>
                <div class="relative">
                    <input :type="showConfirmation ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" required autocomplete="new-password"
                        class="block h-14 w-full rounded-xl border bg-white py-0 pl-4 pr-16 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 @error('password_confirmation') border-red-500 focus:border-red-500 focus:ring-red-500/10 @else border-slate-200 @enderror">
                    <button type="button" @click="showConfirmation = !showConfirmation" :aria-label="showConfirmation ? 'Hide password confirmation' : 'Show password confirmation'" :aria-pressed="showConfirmation.toString()" class="absolute right-2 top-1/2 inline-flex h-10 -translate-y-1/2 items-center rounded-lg px-3 text-sm font-medium text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"><span x-text="showConfirmation ? 'Hide' : 'Show'"></span></button>
                </div>
                @error('password_confirmation')<p class="mt-2 flex items-center gap-1.5 text-sm text-red-600"><span aria-hidden="true">●</span>{{ $message }}</p>@enderror
            </div>

            <fieldset>
                <legend class="mb-3 block text-sm font-medium text-slate-800">Account type</legend>
                <div class="grid gap-3 sm:grid-cols-2" role="radiogroup" aria-label="Account type">
                    <label class="relative block cursor-pointer rounded-xl border p-4 transition duration-200 focus-within:ring-4 focus-within:ring-indigo-500/10" :class="accountType === 'candidate' ? 'border-indigo-600 bg-indigo-50 ring-1 ring-indigo-600' : 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-slate-50'">
                        <input class="sr-only" type="radio" name="account_type" value="candidate" x-model="accountType" required>
                        <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl" :class="accountType === 'candidate' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21.75a17.933 17.933 0 01-7.5-1.632z" /></svg></span>
                        <span class="block text-sm font-semibold text-slate-950">Candidate</span><span class="mt-1 block text-sm leading-5 text-slate-600">I’m looking for a job. Discover opportunities that match your skills.</span>
                        <span x-show="accountType === 'candidate'" x-cloak class="absolute right-3 top-3 flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 text-white"><svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="m3.5 8 3 3 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    </label>
                    <label class="relative block cursor-pointer rounded-xl border p-4 transition duration-200 focus-within:ring-4 focus-within:ring-indigo-500/10" :class="accountType === 'employer' ? 'border-indigo-600 bg-indigo-50 ring-1 ring-indigo-600' : 'border-slate-200 bg-white hover:border-indigo-300 hover:bg-slate-50'">
                        <input class="sr-only" type="radio" name="account_type" value="employer" x-model="accountType">
                        <span class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl" :class="accountType === 'employer' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3.75h15v17.25h-15V3.75zm3 3h1.5m-1.5 3h1.5m-1.5 3h1.5m6-6H16.5m-4.5 3h1.5m3 0h-1.5m-3 3h1.5m3 0h-1.5M9 21v-4.5h6V21" /></svg></span>
                        <span class="block text-sm font-semibold text-slate-950">Employer</span><span class="mt-1 block text-sm leading-5 text-slate-600">I’m hiring. Find candidates for your company.</span>
                        <span x-show="accountType === 'employer'" x-cloak class="absolute right-3 top-3 flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 text-white"><svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none"><path d="m3.5 8 3 3 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                    </label>
                </div>
            </fieldset>

            <button type="submit" :disabled="isSubmitting" class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-base font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 hover:shadow-indigo-600/30 focus:outline-none focus:ring-4 focus:ring-indigo-500/25 disabled:cursor-not-allowed disabled:bg-indigo-400 disabled:shadow-none">
                <svg x-show="isSubmitting" x-cloak class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg><span x-text="isSubmitting ? 'Creating account…' : 'Create account'"></span>
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-slate-600">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-indigo-600 transition hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Log in</a></p>
    </div>
@endsection
