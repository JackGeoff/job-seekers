@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12">

        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/60"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-600">
                    Job marketplace
                </p>

                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                    Find your next opportunity
                </h1>

                <p class="mt-2 text-base text-slate-600">
                    Explore jobs from employers currently hiring.
                </p>
            </div>

            {{-- Search --}}
            <form method="GET"
                  action="{{ route('jobs.index') }}"
                  class="mt-8 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">

                <div class="grid gap-3 lg:grid-cols-[1fr_1fr_auto]">

                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Search jobs, titles or keywords..."
                        class="h-12 rounded-xl border border-slate-200 px-4 text-slate-900 outline-none focus:border-brand-500"
                    >

                    <input
                        type="text"
                        name="location"
                        value="{{ $location }}"
                        placeholder="Location"
                        class="h-12 rounded-xl border border-slate-200 px-4 text-slate-900 outline-none focus:border-brand-500"
                    >

                    <button
                        type="submit"
                        class="brand-btn accent-btn h-12">
                        Search Jobs
                    </button>

                </div>

            </form>

            {{-- Jobs --}}
            <div class="mt-10">

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">
                            Available jobs
                        </p>

                        <h2 class="mt-2 text-2xl font-semibold text-slate-950">
                            Latest opportunities
                        </h2>
                    </div>

                    <p class="text-sm text-slate-500">
                        {{ $jobs->total() }} jobs
                    </p>
                </div>

                @if ($jobs->count())

                    <div class="mt-5 grid gap-4">

                        @foreach ($jobs as $job)

                            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md sm:p-6">

                                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                                    <div>

                                        <h3 class="text-xl font-semibold text-slate-950">
                                            {{ $job->title }}
                                        </h3>

                                        <p class="mt-1 font-medium text-brand-600">
                                            {{ $job->employerProfile->company_name }}
                                        </p>

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

                                        </div>

                                        @if ($job->application_deadline)
                                            <p class="mt-3 text-sm text-slate-500">
                                                Apply by
                                                <span class="font-medium text-slate-700">
                                                    {{ $job->application_deadline->format('M d, Y') }}
                                                </span>
                                            </p>
                                        @endif

                                    </div>

                                    <div class="flex shrink-0 flex-col gap-2 sm:min-w-32">
                                        <a
                                            href="{{ route('jobs.show', $job) }}"
                                            class="rounded-xl border border-brand-200 px-5 py-3 text-center text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                                            View Job
                                        </a>
                                        @auth
                                            @if (auth()->user()->account_type === 'candidate')
                                                <a href="{{ route('candidate.jobs.apply.create', $job) }}" class="brand-btn accent-btn text-center">Apply</a>
                                            @else
                                                <span class="text-center text-xs text-slate-500">Candidates only</span>
                                            @endif
                                        @else
                                            <a href="{{ route('candidate.jobs.apply.create', $job) }}" class="brand-btn accent-btn text-center">Apply</a>
                                        @endauth
                                    </div>

                                </div>

                            </article>

                        @endforeach

                    </div>

                    <div class="mt-8">
                        {{ $jobs->links() }}
                    </div>

                @else

                    <div class="mt-5 rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm">

                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-xl text-brand-700">
                            🔎
                        </div>

                        <h3 class="mt-4 text-lg font-semibold text-slate-950">
                            No jobs found
                        </h3>

                        <p class="mt-2 text-sm text-slate-500">
                            Try changing your search terms or location.
                        </p>

                    </div>

                @endif

            </div>

        </div>

    </section>
@endsection
