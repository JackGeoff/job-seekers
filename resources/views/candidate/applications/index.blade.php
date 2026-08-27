@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12">

        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/50"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">

                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-600">
                        Candidate dashboard
                    </p>

                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                        My Applications
                    </h1>

                    <p class="mt-2 text-base text-slate-600">
                        Track the jobs you've applied for and monitor your application status.
                    </p>
                </div>

                <a
                    href="{{ route('jobs.index') }}"
                    class="brand-btn accent-btn"
                >
                    Browse Jobs
                </a>

            </div>

            @if ($applications->count())

                <div class="mt-8 space-y-4">

                    @foreach ($applications as $application)

                        @php
                            $status = strtolower($application->status ?? 'submitted');

                            $statusClasses = match ($status) {
                                'shortlisted' => 'bg-green-100 text-green-700',
                                'interview' => 'bg-purple-100 text-purple-700',
                                'hired' => 'bg-emerald-100 text-emerald-700',
                                'rejected' => 'bg-red-100 text-red-700',
                                'under_review', 'reviewing' => 'bg-amber-100 text-amber-700',
                                default => 'bg-brand-100 text-brand-700',
                            };

                            $statusLabel = match ($status) {
                                'under_review' => 'Under Review',
                                'reviewing' => 'Under Review',
                                'shortlisted' => 'Shortlisted',
                                'interview' => 'Interview',
                                'hired' => 'Hired',
                                'rejected' => 'Rejected',
                                default => 'Submitted',
                            };
                        @endphp

                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-6">

                            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                                <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-3">

                                        <h2 class="text-xl font-semibold text-slate-950">
                                            {{ $application->job->title }}
                                        </h2>

                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                            {{ $statusLabel }}
                                        </span>

                                    </div>

                                    @if ($application->job->employerProfile)

                                        <p class="mt-2 font-medium text-brand-700">
                                            {{ $application->job->employerProfile->company_name }}
                                        </p>

                                    @endif

                                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-500">

                                        <span>
                                            {{ $application->job->location }}
                                        </span>

                                        <span>
                                            Applied {{ $application->created_at->diffForHumans() }}
                                        </span>

                                    </div>

                                </div>

                                <div class="flex shrink-0 flex-wrap gap-3">

                                    <a
                                        href="{{ route('jobs.show', $application->job) }}"
                                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700"
                                    >
                                        View Job
                                    </a>

                                </div>

                            </div>

                        </article>

                    @endforeach

                </div>

            @else

                <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-12">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-xl text-brand-700">
                        →
                    </div>

                    <h2 class="mt-4 text-lg font-semibold text-slate-950">
                        No applications yet
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        You haven't applied for any jobs yet. Browse available opportunities and submit your first application.
                    </p>

                    <a
                        href="{{ route('jobs.index') }}"
                        class="brand-btn accent-btn mt-6 inline-flex"
                    >
                        Find Jobs
                    </a>

                </div>

            @endif

        </div>

    </section>
@endsection
