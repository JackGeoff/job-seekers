@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/50"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div><p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-600">Candidate dashboard</p><h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Welcome back, {{ auth()->user()->name }}</h1><p class="mt-2 text-base text-slate-600">Find your next opportunity.</p></div>
                <a href="{{ route('candidate.profile') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-accent-200 bg-white px-4 text-sm font-semibold text-accent-600 shadow-sm transition hover:border-accent-400 hover:bg-accent-50">My Profile</a>
            </div>

            <nav class="mt-7 flex gap-2 overflow-x-auto pb-1 text-sm font-medium" aria-label="Candidate dashboard navigation">
                <a href="{{ route('candidate.dashboard') }}" class="shrink-0 rounded-lg bg-brand-600 px-4 py-2 text-white">Dashboard</a><span class="shrink-0 rounded-lg px-4 py-2 text-slate-500">Find Jobs</span><span class="shrink-0 rounded-lg px-4 py-2 text-slate-500">Saved Jobs</span><span class="shrink-0 rounded-lg px-4 py-2 text-slate-500">Applications</span><a href="{{ route('candidate.profile') }}" class="shrink-0 rounded-lg px-4 py-2 text-slate-600 hover:bg-white">My Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="ml-auto shrink-0">@csrf<button type="submit" class="rounded-lg px-4 py-2 text-slate-600 hover:bg-white">Logout</button></form>
            </nav>

            <div class="relative mt-8 overflow-hidden rounded-3xl bg-gradient-to-br from-brand-900 via-brand-700 to-accent-600 p-5 shadow-2xl shadow-brand-900/20 sm:p-8">
                <div class="absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/10 blur-2xl"></div><div class="absolute -bottom-20 left-1/3 h-48 w-48 rounded-full bg-accent-400/25 blur-3xl"></div>
                <div class="relative"><h2 class="text-xl font-semibold text-white sm:text-2xl">Search for your next role</h2><p class="mt-1 text-sm text-white/90">Explore opportunities by title, skill, company, or location.</p>
                    <div class="mt-5 grid gap-3 lg:grid-cols-[1.4fr_1fr_auto]"><label class="relative"><span class="sr-only">Job title, keyword or company</span><span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">⌕</span><input type="text" placeholder="Job title, keyword or company" class="h-14 w-full rounded-xl border-0 bg-white pl-11 pr-4 text-slate-900 outline-none ring-0 placeholder:text-slate-400"></label><label class="relative"><span class="sr-only">Location</span><span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">⌖</span><input type="text" placeholder="Location" class="h-14 w-full rounded-xl border-0 bg-white pl-11 pr-4 text-slate-900 outline-none ring-0 placeholder:text-slate-400"></label><button type="button" class="h-14 rounded-xl bg-accent-500 px-7 font-semibold text-white shadow-lg transition hover:bg-accent-600">Search Jobs</button></div>
                </div>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                @foreach ([['Active Applications', '0', 'bg-brand-100 text-brand-700'], ['Saved Jobs', '0', 'bg-sky-100 text-sky-700'], ['Profile', '70%', 'bg-orange-100 text-orange-700']] as [$label, $value, $color])
                    <div class="surface-card rounded-2xl p-5"><div class="flex items-start justify-between"><p class="text-sm font-medium text-slate-600">{{ $label }}</p><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $color }}">{{ $label === 'Profile' ? '✓' : '○' }}</span></div><p class="mt-4 text-3xl font-semibold text-slate-950">{{ $value }}</p></div>
                @endforeach
            </div>

            <div class="mt-8 grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
                <div><p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-600">Your account</p><h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">Recent Activity</h2><div class="mt-5 rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm"><div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-50 text-xl text-brand-700">⌁</div><p class="mt-4 font-semibold text-slate-900">No recent activity yet.</p><p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Your applications, saved jobs and profile activity will appear here.</p></div></div>
                <aside><p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">Shortcuts</p><h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">Quick Links</h2><div class="mt-5 space-y-3"><a href="{{ route('candidate.profile') }}" class="block rounded-2xl border border-brand-100 bg-white p-4 font-semibold text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:border-brand-300">Complete Profile <span class="float-right text-brand-600">→</span></a><span class="block rounded-2xl border border-slate-200 bg-slate-50 p-4 font-semibold text-slate-500">Browse Jobs <span class="float-right">Soon</span></span><span class="block rounded-2xl border border-slate-200 bg-slate-50 p-4 font-semibold text-slate-500">Saved Jobs <span class="float-right">Soon</span></span><span class="block rounded-2xl border border-slate-200 bg-slate-50 p-4 font-semibold text-slate-500">My Applications <span class="float-right">Soon</span></span></div></aside>
            </div>
        </div>
    </section>
@endsection
