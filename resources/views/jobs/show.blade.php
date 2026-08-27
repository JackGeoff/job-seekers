@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12">

        {{-- Background --}}
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/60"></div>

        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            {{-- Back --}}
            <a
                href="{{ route('jobs.index') }}"
                class="text-sm font-semibold text-brand-600 hover:text-brand-700"
            >
                ← Back to Jobs
            </a>

            {{-- Job Card --}}
            <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-brand-900/5 sm:p-8">

                {{-- Category --}}
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">
                    {{ $job->category }}
                </p>

                {{-- Title --}}
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                    {{ $job->title }}
                </h1>

                {{-- Company --}}
                <p class="mt-2 text-lg font-medium text-brand-600">
                    {{ $job->employerProfile->company_name }}
                </p>

                {{-- Job Meta --}}
                <div class="mt-5 flex flex-wrap gap-3 text-sm">

                    {{-- Location --}}
                    <span class="rounded-full bg-brand-50 px-4 py-2 font-medium text-brand-700">
                        {{ $job->location }}
                    </span>

                    {{-- Employment Type --}}
                    <span class="rounded-full bg-accent-50 px-4 py-2 font-medium text-accent-700">
                        {{ ucwords(str_replace('-', ' ', $job->employment_type)) }}
                    </span>

                    {{-- Salary --}}
                    @if ($job->salary_min || $job->salary_max)
                        <span class="rounded-full bg-slate-100 px-4 py-2 font-medium text-slate-700">

                            {{ $job->salary_currency }}

                            @if ($job->salary_min)
                                {{ number_format($job->salary_min) }}
                            @endif

                            @if ($job->salary_min && $job->salary_max)
                                –
                            @endif

                            @if ($job->salary_max)
                                {{ number_format($job->salary_max) }}
                            @endif

                        </span>
                    @endif

                </div>

                {{-- Job Description --}}
                <div class="mt-8 border-t border-slate-100 pt-8">

                    <h2 class="text-xl font-semibold text-slate-950">
                        Job Description
                    </h2>

                    <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">
                        {{ $job->description }}
                    </div>

                </div>

                {{-- Application Deadline --}}
                @if ($job->application_deadline)

                    <div class="mt-8 rounded-2xl bg-slate-50 p-5">

                        <p class="text-sm font-medium text-slate-500">
                            Application deadline
                        </p>

                        <p class="mt-1 font-semibold text-slate-900">
                            {{ $job->application_deadline->format('F d, Y') }}
                        </p>

                    </div>

                @endif


                {{-- Application Area --}}
                @auth

                    @if (auth()->user()->account_type === 'candidate')

                        <div class="mt-8 border-t border-slate-100 pt-8">

                            {{-- Success Message --}}
                            @if (session('success'))
                                <div class="mb-5 rounded-2xl border border-green-200 bg-green-50 p-4">
                                    <p class="text-sm font-semibold text-green-800">
                                        ✓ Application sent successfully
                                    </p>

                                    <p class="mt-1 text-sm text-green-700">
                                        Your application has been submitted for this position.
                                    </p>
                                </div>
                            @endif


                            {{-- Error Message --}}
                            @if (session('error'))
                                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4">
                                    <p class="text-sm font-semibold text-red-800">
                                        {{ session('error') }}
                                    </p>
                                </div>
                            @endif


                            {{-- Check Whether Candidate Already Applied --}}
                            @php
                                $candidateProfile = auth()->user()->candidateProfile;

                                $alreadyApplied = false;

                                if ($candidateProfile) {
                                    $alreadyApplied = $candidateProfile
                                        ->applications()
                                        ->where('job_id', $job->id)
                                        ->exists();
                                }
                            @endphp


                            {{-- Already Applied --}}
                            @if ($alreadyApplied)

                                <div class="rounded-2xl border border-green-200 bg-green-50 p-5">

                                    <div class="flex items-start gap-3">

                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-700">
                                            ✓
                                        </div>

                                        <div>
                                            <p class="text-sm font-semibold text-green-800">
                                                Application already sent
                                            </p>

                                            <p class="mt-1 text-sm leading-6 text-green-700">
                                                You have already applied for this job. You cannot submit another application.
                                            </p>
                                        </div>

                                    </div>

                                </div>


                            {{-- Not Applied --}}
                            @else

                                <div>

                                    <h2 class="text-xl font-semibold text-slate-950">
                                        Interested in this position?
                                    </h2>

                                    <p class="mt-2 text-sm leading-6 text-slate-600">
                                        Submit your application with your contact information and CV.
                                    </p>

                                    <a
                                        href="{{ route('candidate.jobs.apply.create', $job) }}"
                                        class="brand-btn accent-btn mt-5 inline-flex w-full items-center justify-center sm:w-auto"
                                    >
                                        Apply for this Job
                                    </a>

                                </div>

                            @endif

                        </div>

                    @endif


                @else

                    {{-- Guest --}}
                    <div class="mt-8 rounded-2xl bg-brand-50 p-5">

                        <p class="text-sm font-medium text-brand-800">
                            Please log in as a candidate to apply for this job.
                        </p>

                        <a
                            href="{{ route('login') }}"
                            class="mt-4 inline-flex items-center justify-center rounded-xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-brand-700"
                        >
                            Log In
                        </a>

                    </div>

                @endauth

            </div>

        </div>

    </section>
@endsection
