@extends('layouts.auth')

@section('content')
    <div class="animate-fade-in" x-data="{ isSubmitting: false }">
        <div class="mb-8 sm:mb-10">
            <p class="mb-3 text-sm font-semibold text-indigo-600">EMAIL VERIFICATION</p>

            <h1 class="text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                Verify your email address
            </h1>

            <p class="mt-3 text-base leading-7 text-slate-600">
                Thanks for creating your account. Before you continue, please verify your email address by clicking the link we sent you.
            </p>
        </div>

        @if (session('status') === 'verification-link-sent')
            <div
                class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4"
                role="status"
                aria-live="polite"
            >
                <p class="text-sm font-medium text-emerald-800">
                    A new verification link has been sent to your email address.
                </p>
            </div>
        @endif

        <div class="mb-6 rounded-xl border border-indigo-100 bg-indigo-50 p-5">
            <div class="flex gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6" />
                        <rect x="3" y="5" width="18" height="14" rx="2" />
                    </svg>
                </div>

                <div>
                    <p class="text-sm font-semibold text-slate-900">
                        Check your inbox
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        Look for an email from us and click the verification link to activate your account.
                    </p>
                </div>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('verification.send') }}"
            class="space-y-5"
            @submit="isSubmitting = true"
        >
            @csrf

            <button
                type="submit"
                :disabled="isSubmitting"
                class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-base font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 hover:shadow-indigo-600/30 focus:outline-none focus:ring-4 focus:ring-indigo-500/25 disabled:cursor-not-allowed disabled:bg-indigo-400 disabled:shadow-none"
            >
                <svg
                    x-show="isSubmitting"
                    x-cloak
                    class="h-5 w-5 animate-spin"
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    ></circle>

                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    ></path>
                </svg>

                <span x-text="isSubmitting ? 'Sending…' : 'Resend verification email'"></span>
            </button>
        </form>

        <div class="my-8 flex items-center gap-4" aria-hidden="true">
            <div class="h-px flex-1 bg-slate-200"></div>

            <span class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">
                account
            </span>

            <div class="h-px flex-1 bg-slate-200"></div>
        </div>

        <div class="flex flex-col items-center gap-3 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="min-h-11 inline-flex items-center justify-center text-sm font-semibold text-slate-600 transition hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Log out
                </button>
            </form>

            <p class="text-xs leading-5 text-slate-500">
                If you didn't create this account, you can safely log out.
            </p>
        </div>
    </div>
@endsection
