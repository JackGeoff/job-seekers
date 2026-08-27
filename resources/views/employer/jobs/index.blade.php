@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-accent-50 via-white to-brand-100/75"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">
                        Employer workspace
                    </p>

                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                        My Jobs
                    </h1>

                    <p class="mt-2 text-base text-slate-600">
                        Create, manage and track your job postings.
                    </p>
                </div>

                <a href="{{ route('employer.jobs.create') }}" class="brand-btn accent-btn w-full sm:w-auto">
                    + Post a Job
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="mt-7 flex gap-2 overflow-x-auto pb-1 text-sm font-medium" aria-label="Employer dashboard navigation">

                <a href="{{ route('employer.dashboard') }}"
                   class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white">
                    Dashboard
                </a>

                <a href="{{ route('employer.jobs.index') }}"
                   class="shrink-0 rounded-lg bg-brand-600 px-4 py-2 text-white">
                    Jobs
                </a>

                <span class="shrink-0 rounded-lg px-4 py-2 text-slate-400">
                    Applications
                </span>

                <a href="{{ route('employer.profile') }}"
                   class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white">
                    Company Profile
                </a>

                <form method="POST" action="{{ route('logout') }}" class="ml-auto shrink-0">
                    @csrf

                    <button type="submit"
                            class="rounded-lg px-4 py-2 text-slate-600 hover:bg-white">
                        Logout
                    </button>
                </form>
            </nav>

            {{-- Success message --}}
            @if (session('success'))
                <div class="mt-6 rounded-2xl border border-green-200 bg-green-50 p-4 text-sm font-medium text-green-800"
                     role="alert">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error message --}}
            @if (session('error'))
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800"
                     role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Jobs --}}
            @if ($jobs->count())
                <div class="mt-8 space-y-4">

                    @foreach ($jobs as $job)
                        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-6">

                            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h2 class="text-xl font-semibold text-slate-950">
                                            {{ $job->title }}
                                        </h2>

                                        @if ($job->status === 'published')
                                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                Published
                                            </span>
                                        @elseif ($job->status === 'draft')
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                Draft
                                            </span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                                Closed
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-500">
                                        <span>
                                            {{ $job->location }}
                                        </span>

                                        <span>
                                            {{ ucwords(str_replace('-', ' ', $job->employment_type)) }}
                                        </span>

                                        <span>
                                            {{ $job->category }}
                                        </span>

                                        <span>
                                            Posted {{ $job->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    @if ($job->application_deadline)
                                        <p class="mt-3 text-sm text-slate-500">
                                            Application deadline:
                                            <span class="font-medium text-slate-700">
                                                {{ $job->application_deadline->format('M d, Y') }}
                                            </span>
                                        </p>
                                    @endif
                                </div>

                                <div class="flex flex-wrap gap-2">

                                    <a href="{{ route('employer.jobs.edit', $job) }}"
                                       class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">
                                        Edit
                                    </a>

                                    @if ($job->status !== 'closed')
                                        <form method="POST"
                                              action="{{ route('employer.jobs.close', $job) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
                                                Close
                                            </button>
                                        </form>
                                    @endif

                                    @if ($job->status === 'draft')
                                        <form method="POST"
                                              action="{{ route('employer.jobs.destroy', $job) }}"
                                              onsubmit="return confirm('Delete this draft job? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="rounded-xl border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                                Delete
                                            </button>
                                        </form>
                                    @endif

                                </div>

                            </div>
                        </article>
                    @endforeach

                </div>

                {{-- Pagination --}}
                @if ($jobs->hasPages())
                    <div class="mt-8">
                        {{ $jobs->links() }}
                    </div>
                @endif

            @else

                {{-- Empty state --}}
                <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-12">

                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-accent-50 text-2xl text-accent-600">
                        +
                    </div>

                    <h2 class="mt-5 text-xl font-semibold text-slate-950">
                        No jobs posted yet
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        Create your first job posting and start attracting qualified candidates.
                    </p>

                    <a href="{{ route('employer.jobs.create') }}"
                       class="brand-btn accent-btn mt-6 inline-flex">
                        Post Your First Job
                    </a>

                </div>

            @endif

        </div>
    </section>
@endsection
