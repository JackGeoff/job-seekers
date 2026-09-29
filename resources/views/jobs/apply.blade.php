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
                       @extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12">

        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/60"></div>

        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

            <a href="{{ route('jobs.show', $job) }}"
               class="text-sm font-semibold text-brand-600 hover:text-brand-700">
                ← Back to Job
            </a>

            <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-brand-900/5 sm:p-8">

                {{-- Job Information --}}
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


                {{-- Application --}}
                <div class="mt-8 border-t border-slate-100 pt-8">

                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-slate-950">
                            Submit your application
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Review your details below and submit your application.
                            Your saved profile and CV will be shared with the employer.
                        </p>
                    </div>


                    {{-- Success Message --}}
                    @if (session('success'))
                        <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-800">
                                {{ session('success') }}
                            </p>
                        </div>
                    @endif


                    {{-- Error Messages --}}
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


                    {{-- Candidate Details --}}
                    <div class="space-y-5">

                        {{-- Full Name --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-900">
                                Full Name
                            </label>

                            <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                                {{ $candidateProfile->full_name }}
                            </div>
                        </div>


                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-900">
                                Phone Number
                            </label>

                            <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                                {{ $candidateProfile->phone }}
                            </div>
                        </div>


                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-900">
                                Email Address
                            </label>

                            <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                                {{ auth()->user()->email }}
                            </div>

                            <p class="mt-1 text-xs text-slate-500">
                                This email will be shared with the employer.
                            </p>
                        </div>


                        {{-- Location --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-900">
                                Location
                            </label>

                            <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                                {{ $candidateProfile->location }}
                            </div>
                        </div>


                        {{-- Job Title --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-900">
                                Current / Desired Job Title
                            </label>

                            <div class="mt-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900">
                                {{ $candidateProfile->job_title }}
                            </div>
                        </div>


                        {{-- Saved CV --}}
                        <div class="rounded-2xl border border-brand-100 bg-brand-50/50 p-5">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                                <div>
                                    <p class="font-semibold text-slate-900">
                                        CV / Resume
                                    </p>

                                    <p class="mt-1 text-sm text-slate-600">
                                        Your saved CV will be sent with this application.
                                    </p>
                                </div>


                                <a
                                    href="{{ route('candidate.profile.cv') }}"
                                    target="_blank"
                                    class="inline-flex min-h-11 items-center justify-center rounded-xl border border-brand-200 bg-white px-5 text-sm font-semibold text-brand-700 transition hover:bg-brand-50"
                                >
                                    View CV
                                </a>

                            </div>

                        </div>

                    </div>


                    {{-- Application Form --}}
                    <form
                        method="POST"
                        action="{{ route('candidate.jobs.apply', $job) }}"
                        class="mt-8"
                    >

                        @csrf


                        {{-- Submit --}}
                        <div class="border-t border-slate-100 pt-6">

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                <a
                                    href="{{ route('candidate.profile') }}"
                                    class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                                >
                                    Edit Profile
                                </a>


                                <button
                                    type="submit"
                                    class="brand-btn accent-btn w-full sm:w-auto"
                                >
                                    Submit Application
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </section>
@endsection