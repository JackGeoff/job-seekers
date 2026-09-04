@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12" x-data="{ updatingCv: {{ $hasExistingCv ? 'false' : 'true' }} }">

        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/60"></div>

        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

            <a href="{{ route('jobs.show', $job) }}"
               class="text-sm font-semibold text-brand-600 hover:text-brand-700">
                ← Back to Job
            </a>

            <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-brand-900/5 sm:p-8">

                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">
                        Job Application
                    </p>

                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">
                        {{ $job->title }}
                    </h1>

                    <p class="mt-2 text-base font-medium text-brand-600">
                        {{ $job->employerProfile->company_name }}
                    </p>

                    <div class="mt-4 flex flex-wrap gap-3 text-sm">
                        <span class="rounded-full bg-brand-50 px-4 py-2 font-medium text-brand-700">
                            {{ $job->location }}
                        </span>

                        <span class="rounded-full bg-accent-50 px-4 py-2 font-medium text-accent-700">
                            {{ ucwords(str_replace('-', ' ', $job->employment_type)) }}
                        </span>
                    </div>
                </div>

                <div class="mt-8 border-t border-slate-100 pt-8">

                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-slate-950">
                            Submit your application
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Confirm your details and choose whether to send your saved CV or update it first.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">
                            <p class="text-sm font-semibold text-red-800">
                                Please correct the following:
                            </p>

                            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('candidate.jobs.apply', $job) }}"
                        enctype="multipart/form-data"
                        class="space-y-6"
                    >
                        @csrf

                        @if ($hasExistingCv)
                            <div class="rounded-2xl border border-brand-100 bg-brand-50/50 p-5">
                                <p class="font-semibold text-slate-900">Your saved CV is ready</p>
                                <p class="mt-1 text-sm text-slate-600">Send the CV already saved to your profile, or replace it with a newer version.</p>
                                <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                                    <button type="submit" name="use_existing_cv" value="1" class="brand-btn accent-btn w-full sm:w-auto">Send Your CV</button>
                                    <button type="button" @click="updatingCv = !updatingCv" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-brand-200 bg-white px-5 text-sm font-semibold text-brand-700 transition hover:bg-brand-50" :aria-expanded="updatingCv.toString()">Update CV</button>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label
                                for="full_name"
                                class="block text-sm font-semibold text-slate-900"
                            >
                                Full Name
                            </label>

                            <input
                                id="full_name"
                                name="full_name"
                                type="text"
                                value="{{ old('full_name', $candidateProfile?->full_name ?? auth()->user()->name) }}"
                                required
                                maxlength="255"
                                class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                            >

                            @error('full_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="phone"
                                class="block text-sm font-semibold text-slate-900"
                            >
                                Phone Number
                            </label>

                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                value="{{ old('phone', $candidateProfile?->phone ?? auth()->user()->phone) }}"
                                required
                                maxlength="30"
                                class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                            >

                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                for="email"
                                class="block text-sm font-semibold text-slate-900"
                            >
                                Email Address
                            </label>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', auth()->user()->email) }}"
                                required
                                maxlength="255"
                                class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none"
                            >

                            <p class="mt-1 text-xs text-slate-500">
                                This email will be shared with the employer.
                            </p>

                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="updatingCv" x-transition x-cloak>
                            <label
                                for="cv"
                                class="block text-sm font-semibold text-slate-900"
                            >
                                CV / Resume
                            </label>

                            <input
                                id="cv"
                                name="cv"
                                type="file"
                                accept=".pdf,.doc,.docx"
                                :required="updatingCv"
                                class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700"
                            >

                            <p class="mt-2 text-xs text-slate-500">
                                Accepted formats: PDF, DOC, DOCX. Maximum size: 5 MB.
                            </p>

                            @error('cv')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="border-t border-slate-100 pt-6">

                            @if ($hasExistingCv)
                                <button type="submit" name="use_existing_cv" value="0" x-show="updatingCv" class="brand-btn accent-btn w-full sm:w-auto">Update CV &amp; Send Your CV</button>
                            @else
                                <button type="submit" name="use_existing_cv" value="0" class="brand-btn accent-btn w-full sm:w-auto">Send Application</button>
                            @endif

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </section>
@endsection
