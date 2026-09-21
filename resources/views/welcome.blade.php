<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Find your next opportunity with Kenya's most innovative companies.">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <title>{{ config('app.name', 'Job Seekers') }} | Find your next opportunity</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        [class*="aspect-[1.05/.96]"] { background-image: url("{{ asset('images/hero-image.png') }}") !important; background-position: center; background-size: cover; border-color: rgb(255 255 255 / .58); box-shadow: 0 28px 60px rgb(2 20 44 / .22), inset 0 1px rgb(255 255 255 / .35); backdrop-filter: blur(12px); animation: image-arrive .8s cubic-bezier(.16, 1, .3, 1) .12s both; }
        [class*="aspect-[1.05/.96]"] > div { display: none; }
        @keyframes rise-in { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        .rise-in { animation: rise-in .6s cubic-bezier(.16,1,.3,1) both; }
        .rise-in-delay { animation: rise-in .6s .12s cubic-bezier(.16,1,.3,1) both; }
        .rise-in-late { animation: rise-in .6s .24s cubic-bezier(.16,1,.3,1) both; }
        @media (prefers-reduced-motion: reduce) { .rise-in, .rise-in-delay, .rise-in-late, [class*="aspect-[1.05/.96]"] { animation: none; } }
    </style>
</head>
<body class="min-w-0 overflow-x-hidden bg-[#fafaff] font-sans text-slate-950 antialiased" x-data="{ mobileOpen: false, jobseekersOpen: false, employersOpen: false, profileOpen: false, profile: 'job' }" @keydown.escape.window="mobileOpen = false; jobseekersOpen = false; employersOpen = false; profileOpen = false" :class="{ 'overflow-hidden': profileOpen }">
    <header class="relative z-40 border-b border-indigo-100/70 bg-[#fafaff]/90 backdrop-blur">
        <nav class="mx-auto flex h-18 max-w-7xl items-center justify-between px-5 sm:px-8 lg:px-10" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="group flex items-center rounded-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-600"><img src="{{ asset('images/jobseekers-logo.png') }}" alt="Job Seekers" class="brand-logo"></a>
            <div class="hidden items-center gap-7 lg:flex">
                <div class="relative" @click.outside="jobseekersOpen = false"><button type="button" @click="jobseekersOpen = !jobseekersOpen; employersOpen = false" :aria-expanded="jobseekersOpen.toString()" class="inline-flex items-center gap-1 text-sm font-medium text-slate-600 hover:text-indigo-600">Jobseekers <span aria-hidden="true">⌄</span></button><div x-cloak x-show="jobseekersOpen" x-transition class="absolute left-0 top-full z-50 mt-3 w-52 rounded-xl border border-slate-200 bg-white p-2 shadow-xl"><a href="{{ route('jobs.index') }}" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">Find Jobs</a><a href="{{ route('career-guide') }}" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">Career Guide</a></div></div>
                <div class="relative" @click.outside="employersOpen = false"><button type="button" @click="employersOpen = !employersOpen; jobseekersOpen = false" :aria-expanded="employersOpen.toString()" class="inline-flex items-center gap-1 text-sm font-medium text-slate-600 hover:text-indigo-600">Employers <span aria-hidden="true">⌄</span></button><div x-cloak x-show="employersOpen" x-transition class="absolute left-0 top-full z-50 mt-3 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-xl"><a href="{{ route('employer.register') }}" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">Recruit with Jobseekers</a><a href="{{ route('pricing') }}" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700">Pricing</a></div></div>
                <a href="{{ route('blog.index') }}" class="text-sm font-medium text-slate-600 transition hover:text-indigo-600">Blog</a>
            </div>
            <div class="hidden items-center gap-3 sm:flex">
                @auth
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Logout</button></form>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200 transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Login</a>
                @endauth
            </div>
            <button type="button" class="grid size-11 place-items-center rounded-xl text-slate-700 transition hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-indigo-600 sm:hidden" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen.toString()" aria-controls="mobile-menu" aria-label="Toggle navigation"><svg x-show="!mobileOpen" class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg><svg x-cloak x-show="mobileOpen" class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/></svg></button>
        </nav>
        <div id="mobile-menu" x-cloak x-show="mobileOpen" x-transition class="border-t border-indigo-100 bg-white px-5 py-4 shadow-lg sm:hidden"><div class="mx-auto grid max-w-7xl gap-1"><p class="px-4 pt-2 text-xs font-bold uppercase tracking-wider text-slate-400">Jobseekers</p><a @click="mobileOpen = false" href="{{ route('jobs.index') }}" class="rounded-xl px-4 py-3 text-base font-semibold text-slate-700 hover:bg-indigo-50">Find Jobs</a><a @click="mobileOpen = false" href="{{ route('career-guide') }}" class="rounded-xl px-4 py-3 text-base font-semibold text-slate-700 hover:bg-indigo-50">Career Guide</a><p class="mt-2 px-4 pt-2 text-xs font-bold uppercase tracking-wider text-slate-400">Employers</p><a @click="mobileOpen = false" href="{{ route('employer.register') }}" class="rounded-xl px-4 py-3 text-base font-semibold text-slate-700 hover:bg-indigo-50">Recruit with Jobseekers</a><a @click="mobileOpen = false" href="{{ route('pricing') }}" class="rounded-xl px-4 py-3 text-base font-semibold text-slate-700 hover:bg-indigo-50">Pricing</a><a @click="mobileOpen = false" href="{{ route('blog.index') }}" class="rounded-xl px-4 py-3 text-base font-semibold text-slate-700 hover:bg-indigo-50">Blog</a><div class="mt-3 border-t border-slate-100 pt-4">@auth<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-center text-sm font-semibold text-white">Logout</button></form>@else<a href="{{ route('login') }}" class="block rounded-xl bg-indigo-600 px-4 py-3 text-center text-sm font-semibold text-white">Login</a>@endauth</div></div></div>
    </header>
    <main><section class="relative isolate overflow-hidden"><div class="pointer-events-none absolute left-1/2 top-0 -z-10 h-96 w-96 -translate-x-1/2 rounded-full bg-indigo-100/45 blur-3xl"></div><div class="mx-auto grid max-w-7xl gap-12 px-5 pb-14 pt-14 sm:px-8 sm:pb-20 sm:pt-20 lg:grid-cols-[1.04fr_.96fr] lg:items-center lg:gap-14 lg:px-10 lg:py-24"><div class="min-w-0">
        <p class="rise-in inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-white px-3.5 py-2 text-xs font-bold uppercase tracking-[.12em] text-indigo-700 shadow-sm"><span class="size-1.5 rounded-full bg-emerald-500"></span>Now hiring in Kenya</p><h1 class="rise-in-delay mt-6 max-w-xl text-4xl font-extrabold tracking-[-.055em] text-slate-950 sm:text-5xl sm:leading-[1.02] lg:text-6xl">Find your next<br><span class="text-indigo-600">opportunity</span></h1><p class="rise-in-delay mt-6 max-w-xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">Discover premium job opportunities at Kenya's most innovative companies. Modern roles. Competitive compensation. Real growth.</p>
        <form class="rise-in-late mt-8 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-indigo-950/5" @submit.prevent="profileOpen = true" aria-label="Search jobs"><div class="flex flex-col gap-1 md:flex-row md:items-center"><label class="flex min-w-0 flex-1 items-center gap-3 rounded-xl px-3 py-3 text-slate-400 focus-within:bg-indigo-50/60"><svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg><span class="sr-only">Job title, keywords or company</span><input type="search" class="min-w-0 flex-1 bg-transparent text-sm text-slate-800 outline-none placeholder:text-slate-400" placeholder="Job title, keywords or company"></label><div class="hidden h-8 w-px bg-slate-200 md:block"></div><label class="flex min-w-0 flex-1 items-center gap-3 rounded-xl px-3 py-3 text-slate-400 focus-within:bg-indigo-50/60"><svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z"/><circle cx="12" cy="10" r="2.25" stroke-width="2"/></svg><span class="sr-only">Location</span><input type="search" class="min-w-0 flex-1 bg-transparent text-sm text-slate-800 outline-none placeholder:text-slate-400" placeholder="Location (e.g. Nairobi)"></label><button type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-bold text-white shadow-sm transition duration-200 hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"><svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg><span class="md:hidden">Search Jobs</span><span class="hidden md:inline">Search</span></button></div></form>
        <div class="rise-in-late mt-5 flex flex-col gap-3 sm:flex-row"><a href="{{ route('register') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-bold text-white shadow-md shadow-indigo-200 transition duration-200 hover:-translate-y-0.5 hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Get Started Free <span aria-hidden="true">→</span></a><a href="{{ route('login') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign In</a></div>
        <a href="#latest-jobs" class="rise-in-late mt-8 inline-flex min-h-12 items-center justify-center rounded-xl border border-indigo-200 bg-white px-5 text-sm font-bold text-indigo-700 transition hover:border-indigo-300 hover:bg-indigo-50">Scroll down to view jobs <span class="ml-2" aria-hidden="true">↓</span></a>
    </div><div class="rise-in-delay relative mx-auto w-full max-w-xl lg:max-w-none"><div class="absolute -right-10 top-10 -z-10 size-44 rounded-full bg-violet-200/50 blur-3xl"></div><div class="relative aspect-[1.05/.96] overflow-hidden rounded-[2rem] border-8 border-white bg-gradient-to-br from-indigo-100 via-violet-50 to-fuchsia-100 shadow-2xl shadow-indigo-950/15 sm:rounded-[2.5rem]"><div class="absolute inset-x-0 top-0 h-1/3 bg-white/30"></div><div class="absolute bottom-0 left-[12%] h-[68%] w-[72%] rounded-t-[6rem] bg-indigo-900/90"></div><div class="absolute bottom-[17%] left-[29%] size-[28%] rounded-full bg-amber-700 ring-8 ring-amber-600/25"></div><div class="absolute bottom-[18%] left-[24%] h-[36%] w-[45%] rounded-t-[7rem] bg-amber-700"></div><div class="absolute bottom-[13%] right-[15%] h-[31%] w-[38%] -rotate-6 rounded-2xl bg-slate-800 shadow-2xl"><div class="m-2 h-[calc(100%-1rem)] rounded-xl bg-gradient-to-br from-indigo-300 to-indigo-100"></div></div><div class="absolute inset-x-0 bottom-0 bg-white/85 px-5 py-3 text-center text-[10px] font-bold uppercase tracking-[.14em] text-slate-500">Replace with Kenyan professional photo</div></div><div class="absolute -left-2 top-[16%] flex items-center gap-2 rounded-2xl bg-white px-3 py-2.5 shadow-xl shadow-slate-950/10 sm:-left-7"><span class="grid size-8 place-items-center rounded-xl bg-emerald-100 text-emerald-600">✓</span><div><p class="text-xs font-bold text-slate-800">Application sent</p><p class="text-[10px] text-slate-500">Just now</p></div></div><div class="absolute -right-1 top-[47%] hidden items-center gap-2 rounded-2xl bg-white px-3 py-2.5 shadow-xl shadow-slate-950/10 sm:flex"><span class="grid size-8 place-items-center rounded-xl bg-amber-100 text-amber-500">★</span><div><p class="text-xs font-bold text-slate-800">Top Company</p><p class="text-[10px] text-slate-500">Verified employer</p></div></div><div class="absolute bottom-6 left-5 rounded-2xl bg-white px-3.5 py-2.5 shadow-xl shadow-slate-950/10"><p class="text-xs font-bold text-slate-800">200+ companies hiring</p><div class="mt-1.5 flex -space-x-1.5"><span class="size-5 rounded-full border-2 border-white bg-indigo-400"></span><span class="size-5 rounded-full border-2 border-white bg-violet-400"></span><span class="size-5 rounded-full border-2 border-white bg-fuchsia-400"></span><span class="grid size-5 place-items-center rounded-full border-2 border-white bg-slate-100 text-[8px] font-bold text-slate-500">+</span></div></div></div></div></div></section></main>
    <div x-cloak x-show="profileOpen" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:p-6" role="dialog" aria-modal="true" aria-labelledby="profile-modal-title" @click.self="profileOpen = false"><div x-show="profileOpen" x-transition:enter="transition ease-out duration-250" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-slate-950/45 backdrop-blur-sm"></div><div x-show="profileOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-8 opacity-0 sm:scale-95" x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="translate-y-8 opacity-0 sm:scale-95" class="relative w-full max-w-lg rounded-t-[2rem] bg-white p-6 shadow-2xl sm:rounded-[2rem] sm:p-8"><div class="mx-auto mb-5 h-1.5 w-11 rounded-full bg-slate-200 sm:hidden"></div><button type="button" @click="profileOpen = false" class="absolute right-5 top-5 grid size-10 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus-visible:outline-2 focus-visible:outline-indigo-600" aria-label="Close dialog">✕</button><p class="text-sm font-semibold text-indigo-600">Let’s get started</p><h2 id="profile-modal-title" class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">How can we help?</h2><p class="mt-2 text-sm leading-6 text-slate-500">Choose the experience that best fits your goals.</p><div class="mt-6 grid gap-3"><button type="button" @click="profile = 'job'" :class="profile === 'job' ? 'border-indigo-600 bg-indigo-50 ring-1 ring-indigo-600' : 'border-slate-200 bg-white hover:border-indigo-200'" class="flex items-start gap-4 rounded-2xl border p-4 text-left transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"><span class="grid size-11 shrink-0 place-items-center rounded-xl bg-white text-xl shadow-sm">👤</span><span class="min-w-0 flex-1"><span class="block text-sm font-bold text-slate-900">I'm looking for a job</span><span class="mt-1 block text-sm leading-5 text-slate-500">Find opportunities that match your skills.</span></span><span x-show="profile === 'job'" class="text-indigo-600" aria-label="Selected">✓</span></button><button type="button" @click="profile = 'hiring'" :class="profile === 'hiring' ? 'border-indigo-600 bg-indigo-50 ring-1 ring-indigo-600' : 'border-slate-200 bg-white hover:border-indigo-200'" class="flex items-start gap-4 rounded-2xl border p-4 text-left transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"><span class="grid size-11 shrink-0 place-items-center rounded-xl bg-white text-xl shadow-sm">🏢</span><span class="min-w-0 flex-1"><span class="block text-sm font-bold text-slate-900">I'm hiring</span><span class="mt-1 block text-sm leading-5 text-slate-500">Find candidates for your company.</span></span><span x-show="profile === 'hiring'" class="text-indigo-600" aria-label="Selected">✓</span></button></div><a href="{{ route('register') }}" class="mt-6 inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-indigo-600 px-5 text-sm font-bold text-white transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Continue <span class="ml-2" aria-hidden="true">→</span></a></div></div>
<section id="latest-jobs" class="border-t border-indigo-100/70 bg-white py-14 sm:py-20">
    <div class="mx-auto max-w-7xl px-5 sm:px-8 lg:px-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[.12em] text-indigo-600">Latest Job Opportunities</p>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-950">Find a role that fits your next move</h2>
                <p class="mt-2 text-base text-slate-600">Browse current openings from employers hiring now.</p>
            </div>
            <a href="{{ route('jobs.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-indigo-200 px-4 text-sm font-bold text-indigo-700 transition hover:bg-indigo-50">View All Jobs <span class="ml-2" aria-hidden="true">→</span></a>
        </div>

        @if ($jobs->isNotEmpty())
            <div class="mt-8 space-y-4">
                @foreach ($jobs as $job)
                    <article class="group flex w-full flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-950/5 sm:p-6 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <p class="text-xs font-bold uppercase tracking-[.12em] text-indigo-600">{{ $job->category }}</p>
                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-emerald-700">Hiring</span>
                            </div>
                            <h3 class="mt-3 text-xl font-extrabold tracking-tight text-slate-950 transition group-hover:text-indigo-700 sm:text-2xl">{{ $job->title }}</h3>
                            <p class="mt-1 text-sm font-semibold text-slate-700">{{ $job->employerProfile?->company_name }}</p>

                            <div class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-500">
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 21s7-4.35 7-11a7 7 0 1 0-14 0c0 6.65 7 11 7 11Z"/><circle cx="12" cy="10" r="2" stroke-width="1.8"/></svg>
                                    {{ $job->location }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7h-3V5.5A1.5 1.5 0 0 0 15.5 4h-7A1.5 1.5 0 0 0 7 5.5V7H4a2 2 0 0 0-2 2v8.5A2.5 2.5 0 0 0 4.5 20h15a2.5 2.5 0 0 0 2.5-2.5V9a2 2 0 0 0-2-2ZM9 7V6h6v1"/></svg>
                                    {{ ucwords(str_replace('-', ' ', $job->employment_type)) }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path stroke-linecap="round" stroke-width="1.8" d="M12 7v5l3 2"/></svg>
                                    Posted {{ $job->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <p class="mt-4 line-clamp-2 max-w-3xl text-sm leading-6 text-slate-600">{{ $job->description }}</p>

                            <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                                @if ($job->salary_min || $job->salary_max)
                                    <span class="font-bold text-slate-800">
                                        {{ $job->salary_currency }}
                                        @if ($job->salary_min) {{ number_format($job->salary_min) }} @endif
                                        @if ($job->salary_min && $job->salary_max) - @endif
                                        @if ($job->salary_max) {{ number_format($job->salary_max) }} @endif
                                    </span>
                                @endif
                                @if ($job->application_deadline)
                                    <span class="text-slate-500">Apply by <span class="font-semibold text-slate-700">{{ $job->application_deadline->format('M d, Y') }}</span></span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('jobs.show', $job) }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl bg-indigo-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 lg:ml-8 lg:mt-0 lg:shrink-0">View Job <span class="ml-2" aria-hidden="true">-></span></a>
                    </article>
                @endforeach
            </div>
        @else
            <p class="mt-8 rounded-2xl border border-slate-200 bg-[#fafaff] p-6 text-sm text-slate-600">New opportunities will appear here as employers publish them.</p>
        @endif
    </div>
</section>
<script>
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('form[aria-label="Search jobs"]');

        if (!form) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        const inputs = form.querySelectorAll('input');
        inputs[0].name = 'search';
        inputs[1].name = 'location';

        const params = new URLSearchParams(new FormData(form));
        window.location.assign(`{{ route('jobs.index') }}?${params.toString()}`);
    }, true);
</script>
</body>
</html>
