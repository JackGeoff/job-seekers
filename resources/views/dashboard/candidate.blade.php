@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12">

        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/50"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-600">
                        Candidate dashboard
                    </p>

                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                        Welcome back, {{ auth()->user()->name }}
                    </h1>

                    <p class="mt-2 text-base text-slate-600">
                        Find your next opportunity.
                    </p>
                </div>

                <a
                    href="{{ route('candidate.profile') }}"
                    class="inline-flex min-h-11 items-center justify-center rounded-xl border border-accent-200 bg-white px-4 text-sm font-semibold text-accent-600 shadow-sm transition hover:border-accent-400 hover:bg-accent-50"
                >
                    My Profile
                </a>

            </div>

            {{-- Dashboard Navigation --}}
            <nav
                class="mt-7 flex gap-2 overflow-x-auto pb-1 text-sm font-medium"
                aria-label="Candidate dashboard navigation"
            >

                <a
                    href="{{ route('candidate.dashboard') }}"
                    class="shrink-0 rounded-lg bg-brand-600 px-4 py-2 text-white"
                >
                    Dashboard
                </a>

                <a
                    href="{{ route('jobs.index') }}"
                    class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white"
                >
                    Find Jobs
                </a>

                <a
                    href="{{ route('candidate.applications.index') }}"
                    class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white"
                >
                    My Applications
                </a>

                <a
                    href="{{ route('candidate.profile') }}"
                    class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white"
                >
                    My Profile
                </a>

            </nav>

            {{-- Search --}}
            <div class="relative mt-8 overflow-hidden rounded-3xl bg-gradient-to-br from-brand-900 via-brand-700 to-accent-600 p-5 shadow-2xl shadow-brand-900/20 sm:p-8">

                <div class="absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div>

                <div class="absolute -bottom-20 left-1/3 h-48 w-48 rounded-full bg-accent-400/25 blur-3xl"></div>

                <div class="relative">

                    <h2 class="text-xl font-semibold text-white sm:text-2xl">
                        Search for your next role
                    </h2>

                    <p class="mt-1 text-sm text-white/90">
                        Explore opportunities by title, skill, company, or location.
                    </p>

                    <form
                        method="GET"
                        action="{{ route('jobs.index') }}"
                        class="mt-5 grid gap-3 lg:grid-cols-[1.4fr_1fr_auto]"
                    >

                        <input
                            type="text"
                            name="q"
                            value="{{ $search }}"
                            placeholder="Job title, keyword or company"
                            class="h-14 w-full rounded-xl border-0 bg-white px-4 text-slate-900 outline-none placeholder:text-slate-400"
                        >

                        <input
                            type="text"
                            name="location"
                            value="{{ $location }}"
                            placeholder="Location"
                            class="h-14 w-full rounded-xl border-0 bg-white px-4 text-slate-900 outline-none placeholder:text-slate-400"
                        >

                        <button
                            type="submit"
                            class="h-14 rounded-xl bg-accent-500 px-7 font-semibold text-white shadow-lg transition hover:bg-accent-600"
                        >
                            Search Jobs
                        </button>

                    </form>

                </div>

            </div>

            {{-- Stats --}}
            <div class="mt-10 grid gap-4 sm:grid-cols-3">

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-sm font-medium text-slate-600">
                        Applications
                    </p>

                    <p class="mt-3 text-3xl font-semibold text-slate-950">
                        {{ $applicationCount }}
                    </p>

                    <a
                        href="{{ route('candidate.applications.index') }}"
                        class="mt-3 inline-block text-sm font-semibold text-brand-600 hover:text-brand-700"
                    >
                        View applications →
                    </a>

                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-sm font-medium text-slate-600">
                        Under Review
                    </p>

                    <p class="mt-3 text-3xl font-semibold text-slate-950">
                        {{ $underReviewCount }}
                    </p>

                    <p class="mt-3 text-sm text-slate-500">
                        Applications being reviewed by employers.
                    </p>

                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <p class="text-sm font-medium text-slate-600">
                        Profile Completion
                    </p>

                    <p class="mt-3 text-3xl font-semibold text-slate-950">
                        {{ $profileCompletion }}%
                    </p>

                    <a
                        href="{{ route('candidate.profile') }}"
                        class="mt-3 inline-block text-sm font-semibold text-brand-600 hover:text-brand-700"
                    >
                        Update profile →
                    </a>

                </div>

            </div>

            {{-- Opportunities --}}
            <div id="available-jobs" class="mt-12">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">
                            Opportunities
                        </p>

                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">
                            Latest jobs
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Explore the latest opportunities from employers.
                        </p>
                    </div>

                    <a
                        href="{{ route('jobs.index') }}"
                        class="text-sm font-semibold text-brand-600 hover:text-brand-700"
                    >
                        View all jobs →
                    </a>

                </div>

                @if ($jobs->count())

                    <div class="mt-6 space-y-4">

                        @foreach ($jobs as $job)

                            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-6">

                                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                                    <div class="min-w-0">

                                        <div class="flex flex-wrap items-center gap-2">

                                            <h3 class="text-xl font-semibold text-slate-950">
                                                {{ $job->title }}
                                            </h3>

                                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                Hiring
                                            </span>

                                        </div>

                                        @if ($job->employerProfile)

                                            <p class="mt-2 font-medium text-brand-700">
                                                {{ $job->employerProfile->company_name }}
                                            </p>

                                        @endif

                                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-500">

                                            <span>{{ $job->location }}</span>

                                            <span>
                                                {{ ucwords(str_replace('-', ' ', $job->employment_type)) }}
                                            </span>

                                            <span>{{ $job->category }}</span>

                                        </div>

                                        @if ($job->salary_min || $job->salary_max)

                                            <p class="mt-3 text-sm font-semibold text-slate-700">

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

                                            </p>

                                        @endif

                                        <p class="mt-2 text-sm text-slate-500">
                                            Posted {{ $job->created_at->diffForHumans() }}
                                        </p>

                                    </div>

                                    <div class="shrink-0">

                                        <a
                                            href="{{ route('jobs.show', $job) }}"
                                            class="brand-btn accent-btn inline-flex"
                                        >
                                            View Job
                                        </a>

                                    </div>

                                </div>

                            </article>

                        @endforeach

                    </div>

                @else

                    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-12">

                        <h3 class="text-lg font-semibold text-slate-950">
                            No jobs found
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                            Try a different job title, keyword, company or location.
                        </p>

                        @if ($search || $location)

                            <a
                                href="{{ route('candidate.dashboard') }}"
                                class="mt-5 inline-flex rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                            >
                                Clear Search
                            </a>

                        @endif

                    </div>

                @endif

            </div>

        </div>

    </section>
@endsection
