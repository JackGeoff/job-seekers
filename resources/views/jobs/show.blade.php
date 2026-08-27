@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12">

        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/60"></div>

        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            <a href="{{ route('jobs.index') }}"
               class="text-sm font-semibold text-brand-600 hover:text-brand-700">
                ← Back to Jobs
            </a>

            <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-brand-900/5 sm:p-8">

                {{-- Job category --}}
                @if ($job->category)
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">
                        {{ $job->category }}
                    </p>
                @endif

                {{-- Job title --}}
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                    {{ $job->title }}
                </h1>

                {{-- Company --}}
                @if ($job->employerProfile)
                    <p class="mt-2 text-lg font-medium text-brand-600">
                        {{ $job->employerProfile->company_name }}
                    </p>
                @endif

                {{-- Job details --}}
                <div class="mt-5 flex flex-wrap gap-3 text-sm">

                    @if ($job->location)
                        <span class="rounded-full bg-brand-50 px-4 py-2 font-medium text-brand-700">
                            {{ $job->location }}
                        </span>
                    @endif

                    @if ($job->employment_type)
                        <span class="rounded-full bg-accent-50 px-4 py-2 font-medium text-accent-700">
                            {{ ucwords(str_replace('-', ' ', $job->employment_type)) }}
                        </span>
                    @endif

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

                {{-- Job description --}}
                <div class="mt-8 border-t border-slate-100 pt-8">

                    <h2 class="text-xl font-semibold text-slate-950">
                        Job Description
                    </h2>

                    <div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">
                        {{ $job->description }}
                    </div>

                </div>

                {{-- Application deadline --}}
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


                {{-- ========================================================= --}}
                {{-- APPLICATION SECTION --}}
                {{-- ========================================================= --}}

                @auth

                    @if (auth()->user()->account_type === 'candidate')

                        @php
                            $candidateProfile = auth()->user()->candidateProfile;

                            $alreadyApplied = false;

                            if ($candidateProfile) {
                                $alreadyApplied = $candidateProfile->applications()
                                    ->where('job_id', $job->id)
                                    ->exists();
                            }

                            $deadlinePassed = $job->application_deadline
                                && $job->application_deadline->isPast();

                            $jobClosed = $job->status !== 'published';
                        @endphp

                        <div class="mt-8 border-t border-slate-100 pt-8">

                            {{-- Success message --}}
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


                            {{-- Error message --}}
                            @if (session('error'))

                                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4">
                                    <p class="text-sm font-semibold text-red-800">
                                        {{ session('error') }}
                                    </p>
                                </div>

                            @endif


                            {{-- Already applied --}}
                            @if ($alreadyApplied)

                                <div class="rounded-2xl border border-green-200 bg-green-50 p-5">

                                    <div class="flex items-start gap-3">

                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100 text-green-700">
                                            ✓
                                        </div>

                                        <div>
                                            <p class="font-semibold text-green-900">
                                                Application sent
                                            </p>

                                            <p class="mt-1 text-sm text-green-700">
                                                You have already applied for this job.
                                            </p>

                                            <p class="mt-2 text-xs text-green-600">
                                                You cannot submit another application for the same position.
                                            </p>
                                        </div>

                                    </div>

                                </div>


                            {{-- Job closed --}}
                            @elseif ($jobClosed)

                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                    <p class="font-semibold text-slate-800">
                                        Applications are closed
                                    </p>

                                    <p class="mt-1 text-sm text-slate-600">
                                        This job is no longer accepting applications.
                                    </p>

                                </div>


                            {{-- Deadline passed --}}
                            @elseif ($deadlinePassed)

                                <div class="rounded-2xl border border-red-200 bg-red-50 p-5">

                                    <p class="font-semibold text-red-800">
                                        Application deadline passed
                                    </p>

                                    <p class="mt-1 text-sm text-red-700">
                                        This position is no longer accepting applications.
                                    </p>

                                </div>


                            {{-- Candidate has not applied --}}
                            @else

                                <div class="rounded-2xl border border-brand-100 bg-brand-50/50 p-5 sm:p-6">

                                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                                        <div>

                                            <h2 class="text-lg font-semibold text-slate-950">
                                                Interested in this position?
                                            </h2>

                                            <p class="mt-1 text-sm text-slate-600">
                                                Submit your application and CV to apply for this job.
                                            </p>

                                        </div>

                                        <a
                                            href="{{ route('candidate.jobs.apply.create', $job) }}"
                                            class="brand-btn accent-btn inline-flex w-full items-center justify-center sm:w-auto"
                                        >
                                            Apply for this Job
                                        </a>

                                    </div>

                                </div>

                            @endif

                        </div>


                    @else

                        {{-- Logged-in employer viewing a job --}}
                        <div class="mt-8 border-t border-slate-100 pt-8">

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                <p class="font-semibold text-slate-800">
                                    Candidate applications only
                                </p>

                                <p class="mt-1 text-sm text-slate-600">
                                    You are currently logged in with an employer account.
                                    Switch to a candidate account to apply for jobs.
                                </p>

                            </div>

                        </div>

                    @endif


                @else

                    {{-- Not authenticated --}}
                    <div class="mt-8 border-t border-slate-100 pt-8">

                        <div class="rounded-2xl bg-brand-50 p-5">

                            <p class="font-semibold text-brand-900">
                                Ready to apply?
                            </p>

                            <p class="mt-1 text-sm text-brand-700">
                                Please log in as a candidate to apply for this job.
                            </p>

                            <a
                                href="{{ route('login') }}"
                                class="brand-btn accent-btn mt-4 inline-flex"
                            >
                                Log In to Apply
                            </a>

                        </div>

                    </div>

                @endauth

            </div>

        </div>

    </section>
@endsection
