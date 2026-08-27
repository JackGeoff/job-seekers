@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-accent-50 via-white to-brand-100/75"></div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">Employer workspace</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Applications</h1>
                    <p class="mt-2 text-base text-slate-600">Review candidates who have applied to your jobs.</p>
                </div>
            </div>

            <nav class="mt-7 flex gap-2 overflow-x-auto pb-1 text-sm font-medium" aria-label="Employer dashboard navigation">
                <a href="{{ route('employer.dashboard') }}" class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white">Dashboard</a>
                <a href="{{ route('employer.jobs.index') }}" class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white">Jobs</a>
                <a href="{{ route('employer.applications.index') }}" class="shrink-0 rounded-lg bg-brand-600 px-4 py-2 text-white">Applications</a>
                <a href="{{ route('employer.profile') }}" class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white">Company Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="ml-auto shrink-0">
                    @csrf
                    <button type="submit" class="rounded-lg px-4 py-2 text-slate-600 hover:bg-white">Logout</button>
                </form>
            </nav>

            @if ($applications->isEmpty())
                <div class="mt-10 rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-sm sm:p-12">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-50 text-xl text-accent-600">+</div>
                    <h2 class="mt-4 text-xl font-semibold text-slate-950">No applications yet</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">When candidates apply to your published jobs, their applications will appear here.</p>
                    <a href="{{ route('employer.jobs.index') }}" class="brand-btn accent-btn mt-6">View Jobs</a>
                </div>
            @else
                <div class="mt-10 grid gap-4 lg:grid-cols-2">
                    @foreach ($applications as $application)
                        <article class="surface-card rounded-2xl p-5 sm:p-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-accent-600">Applicant</p>
                                    <h2 class="mt-2 text-xl font-semibold text-slate-950">
                                        {{ $application->candidateProfile->full_name }}
                                    </h2>
                                </div>
                                <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold capitalize text-brand-700">
                                    {{ $application->status }}
                                </span>
                            </div>

                            <dl class="mt-5 space-y-2 text-sm text-slate-600">
                                <div class="flex flex-wrap gap-2">
                                    <dt class="font-semibold text-slate-900">Phone:</dt>
                                    <dd>{{ $application->candidateProfile->phone }}</dd>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <dt class="font-semibold text-slate-900">Email:</dt>
                                    <dd>{{ $application->candidateProfile->user->email }}</dd>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <dt class="font-semibold text-slate-900">Applied For:</dt>
                                    <dd>{{ $application->job->title }}</dd>
                                </div>
                            </dl>

                            @if ($application->candidateProfile->cv_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($application->candidateProfile->cv_path) }}" target="_blank" rel="noopener" class="brand-btn accent-btn mt-6 w-full sm:w-auto">
                                    View CV
                                </a>
                            @else
                                <p class="mt-6 text-sm font-medium text-slate-500">CV not provided</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
