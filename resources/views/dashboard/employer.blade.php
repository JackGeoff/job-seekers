@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-accent-50 via-white to-brand-100/75"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">Employer dashboard</p><h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Welcome back, {{ auth()->user()->name }}</h1><p class="mt-2 text-base text-slate-600">Manage your hiring and find the right talent.</p></div><a href="{{ route('employer.jobs.create') }}" class="brand-btn accent-btn">
    + Post a Job
</a></div>
            <nav class="mt-7 flex gap-4 overflow-x-auto pb-1 text-sm font-medium" aria-label="Employer dashboard navigation"><a href="{{ route('employer.dashboard') }}" class="shrink-0 rounded-lg bg-brand-600 px-4 py-2 text-white">Dashboard</a><a href="{{ route('employer.pricing') }}" class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white">Pricing</a><a href="{{ route('employer.jobs.index') }}"
   class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white">
    Jobs
</a><a href="{{ route('employer.applications.index') }}" class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white">Applications</a><a href="{{ route('employer.profile') }}" class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white">Company Profile</a><form method="POST" action="{{ route('logout') }}" class="ml-auto shrink-0">@csrf<button type="submit" class="rounded-lg px-4 py-2 text-slate-600 hover:bg-white">Logout</button></form></nav>
            <div class="mt-8 overflow-hidden rounded-3xl bg-brand-900 p-6 text-white shadow-2xl shadow-brand-900/20 sm:p-8"><div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-sm font-semibold uppercase tracking-[0.15em] text-brand-100">Hiring workspace</p><h2 class="mt-2 text-2xl font-semibold">Build the team that moves your business forward.</h2><p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">Your jobs, candidates and applications will live in one focused workspace.</p></div><span class="rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm text-brand-100">Ready when you are</span></div></div>
            <div class="mt-8 grid gap-4 sm:grid-cols-3">@foreach ([['Active Jobs', $activeJobCount, 'bg-brand-100 text-brand-700'], ['Applications', $applicationCount, 'bg-accent-100 text-accent-600'], ['Candidates', $candidateCount, 'bg-sky-100 text-sky-700']] as [$label, $value, $color])<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><div class="flex items-start justify-between"><p class="text-sm font-medium text-slate-600">{{ $label }}</p><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $color }}">+</span></div><p class="mt-4 text-3xl font-semibold text-slate-950">{{ $value }}</p></div>@endforeach</div>
            <section class="mt-5 rounded-2xl border border-brand-100 bg-white p-5 shadow-sm" aria-labelledby="subscription-overview-heading">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-brand-700">Subscription overview</p>
                        <h2 id="subscription-overview-heading" class="mt-2 text-xl font-semibold text-slate-950">
                            {{ $subscription ? ucfirst($subscription->plan) . ' Plan' : 'No active plan' }}
                        </h2>
                    </div>
                    <a href="{{ route('employer.pricing') }}" class="rounded-xl border border-brand-200 px-4 py-2 text-sm font-semibold text-brand-700 transition hover:bg-brand-50">
                        {{ $subscriptionStatus === 'active' ? 'Upgrade plan' : 'Renew or choose a plan' }}
                    </a>
                </div>

                @if ($subscription)
                    <div class="mt-5 grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                        <p><span class="block text-slate-500">Status</span><strong class="mt-1 inline-block capitalize text-slate-950">{{ $subscriptionStatus }}</strong></p>
                        <p><span class="block text-slate-500">Jobs used</span><strong class="mt-1 inline-block text-slate-950">{{ $jobsUsed }} / {{ $subscription->job_allowance }}</strong></p>
                        <p><span class="block text-slate-500">Jobs remaining</span><strong class="mt-1 inline-block text-slate-950">{{ $jobsRemaining }}</strong></p>
                        <p><span class="block text-slate-500">Subscription period</span><strong class="mt-1 inline-block text-slate-950">{{ $subscription->starts_at?->format('j M Y') ?? 'Not recorded' }} – {{ $subscription->expires_at?->format('j M Y') ?? 'Contract terms' }}</strong></p>
                    </div>

                    <div class="mt-5">
                        <div class="mb-2 flex justify-between text-xs font-medium text-slate-600"><span>Posting credits used</span><span>{{ $usagePercentage }}%</span></div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-label="Posting credits used" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $usagePercentage }}">
                            <div class="h-full rounded-full bg-brand-600 transition-all" style="width: {{ $usagePercentage }}%"></div>
                        </div>
                        @if ($subscription->expires_at)
                            <p class="mt-2 text-xs text-slate-500">{{ $subscriptionStatus === 'expired' ? 'Expired' : 'Expires' }} {{ $subscription->expires_at->diffForHumans() }} ({{ $subscription->expires_at->format('j M Y') }})</p>
                        @endif
                    </div>
                @endif

                @if ($subscriptionMessage)
                    <p class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900" role="status">{{ $subscriptionMessage }}</p>
                @endif
            </section>
            <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]"><div><p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-600">Hiring activity</p><h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">Recent Activity</h2><div class="mt-5 rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm"><div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-accent-50 text-xl text-accent-600">+</div><p class="mt-4 font-semibold text-slate-900">No recent activity yet.</p><p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Job postings, applications and candidate activity will appear here.</p></div></div><aside><p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">Shortcuts</p><h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">Quick Links</h2><div class="mt-5 space-y-3"><a href="{{ route('employer.jobs.create') }}" class="block rounded-2xl border border-accent-100 bg-accent-50 p-4 font-semibold text-accent-700 transition hover:-translate-y-0.5 hover:border-accent-300">Post a Job <span class="float-right">→</span></a><a href="{{ route('employer.jobs.index') }}" class="block rounded-2xl border border-slate-200 bg-slate-50 p-4 font-semibold text-slate-700 transition hover:-translate-y-0.5 hover:border-brand-300">Manage Jobs <span class="float-right text-brand-600">→</span></a><a href="{{ route('employer.applications.index') }}" class="block rounded-2xl border border-accent-100 bg-accent-50 p-4 font-semibold text-accent-700 transition hover:-translate-y-0.5 hover:border-accent-300">Applications <span class="float-right">→</span></a><a href="{{ route('employer.profile') }}" class="block rounded-2xl border border-brand-100 bg-white p-4 font-semibold text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-300">Company Profile <span class="float-right text-brand-600">→</span></a></div></aside></div>
        </div>
    </section>
@endsection
