<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Job seekers — discover opportunities and build your future.">
    <title>{{ config('app.name', 'Job seekers') }}@if (isset($title)) — {{ $title }}@endif</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <main class="relative isolate min-h-screen overflow-hidden lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(30rem,0.9fr)]">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-80 bg-gradient-to-b from-indigo-100/80 via-slate-50 to-transparent lg:hidden"></div>

        <section class="flex min-h-screen flex-col px-5 py-6 sm:px-8 lg:px-12 xl:px-20">
            <a href="{{ route('home') }}" class="inline-flex w-fit items-center rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-4">
                <img src="{{ asset('images/jobseekers-logo.png') }}" alt="Job Seekers" class="brand-logo brand-logo--auth">
            </a>

            <div class="flex flex-1 items-center justify-center py-10 sm:py-14 lg:py-10">
                <div class="w-full max-w-md">
                    @yield('content')
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 lg:text-left">&copy; {{ date('Y') }} {{ config('app.name', 'Job seekers') }}. Find work that moves you forward.</p>
        </section>

        <aside class="relative hidden overflow-hidden bg-brand-900 px-12 py-14 text-white lg:flex xl:px-16">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_82%_16%,rgba(49,134,232,0.42),transparent_25%),radial-gradient(circle_at_12%_90%,rgba(249,115,22,0.2),transparent_34%)]"></div>
            <div class="absolute -right-28 top-28 h-80 w-80 rounded-full border border-indigo-300/15"></div>
            <div class="absolute -bottom-36 -left-24 h-96 w-96 rounded-full border border-sky-300/10"></div>

            <div class="relative flex w-full flex-col justify-between">
                <span class="inline-flex w-fit items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-medium text-indigo-100 backdrop-blur">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Career momentum, made simple
                </span>

                <div class="max-w-md">
                    <div class="auth-image-frame mb-10">
                        <img src="{{ asset(app()->environment('production') ? 'wp/images/register.png' : 'images/register.png') }}" alt="A professional preparing for their next opportunity" class="auth-image">
                    </div>
                    <p class="text-sm font-medium text-brand-100">JOB SEEKERS</p>
                    <h2 class="mt-4 text-4xl font-semibold leading-[1.08] tracking-tight xl:text-5xl">Find opportunities.<br>Build your future.</h2>
                    <p class="mt-5 max-w-sm text-base leading-7 text-slate-300">A clearer path from possibility to your next great role.</p>

                    <div class="mt-10 space-y-3" aria-hidden="true">
                        <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.07] p-4 shadow-2xl shadow-black/10 backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:bg-white/10">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-400/20 text-indigo-100"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21.75a17.933 17.933 0 01-7.5-1.632z" /></svg></span>
                            <div class="min-w-0"><div class="h-2.5 w-24 rounded-full bg-white/80"></div><div class="mt-2 h-2 w-40 max-w-full rounded-full bg-white/20"></div></div><span class="ml-auto h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        </div>
                        <div class="ml-8 flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.06] p-4 backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:bg-white/10">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-400/15 text-sky-100"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3.75h15v17.25h-15V3.75zm3 3h1.5m-1.5 3h1.5m-1.5 3h1.5m6-6H16.5m-4.5 3h1.5m3 0h-1.5m-3 3h1.5m3 0h-1.5M9 21v-4.5h6V21" /></svg></span>
                            <div class="min-w-0"><div class="h-2.5 w-32 rounded-full bg-white/75"></div><div class="mt-2 h-2 w-28 rounded-full bg-white/20"></div></div><span class="ml-auto rounded-full border border-indigo-200/25 px-2 py-1 text-[10px] font-medium text-indigo-100">Matched</span>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-slate-400">Built for people shaping what comes next.</p>
            </div>
        </aside>
    </main>
</body>
</html>
