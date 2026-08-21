@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50" x-data="dashboardModule()">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">

        <!-- Welcome -->
        <div class="mb-8">
            <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600 mb-2">
                Dashboard
            </p>

            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-950">
                Welcome back, {{ Auth::user()->name }}!
            </h1>

            <p class="mt-2 text-base sm:text-lg text-slate-600">
                Discover opportunities that match your skills and goals.
            </p>
        </div>


        <!-- JOB SEARCH CARD -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-700 via-indigo-700 to-blue-900 p-5 sm:p-7 mb-10 shadow-xl shadow-blue-900/10">

            <!-- Decorative gradients -->
            <div class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 h-56 w-56 rounded-full bg-blue-400/20 blur-3xl"></div>

            <div class="relative">
                <div class="mb-5">
                    <h2 class="text-xl sm:text-2xl font-bold text-white">
                        Find your next opportunity
                    </h2>

                    <p class="mt-1 text-sm sm:text-base text-blue-100">
                        Search thousands of jobs by role, skill, or location.
                    </p>
                </div>

                <form class="grid grid-cols-1 lg:grid-cols-[1fr_1fr_auto] gap-3">

                    <!-- Role / Keyword -->
                    <div class="relative">
                        <label for="job-search" class="sr-only">
                            Job title or keyword
                        </label>

                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-slate-400"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.8">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/>
                            </svg>
                        </div>

                        <input
                            type="text"
                            id="job-search"
                            name="search"
                            placeholder="Job title, keyword or skill"
                            class="h-14 w-full rounded-xl border border-white/20 bg-white px-4 pl-12 pr-4 text-base text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-white focus:ring-4 focus:ring-white/20"
                        >
                    </div>


                    <!-- Location -->
                    <div class="relative">
                        <label for="job-location" class="sr-only">
                            Location
                        </label>

                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <svg class="h-5 w-5 text-slate-400"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="1.8">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M12 21s7-5.25 7-12a7 7 0 1 0-14 0c0 6.75 7 12 7 12Z"/>
                                <circle cx="12" cy="9" r="2.5"/>
                            </svg>
                        </div>

                        <input
                            type="text"
                            id="job-location"
                            name="location"
                            placeholder="Location"
                            class="h-14 w-full rounded-xl border border-white/20 bg-white px-4 pl-12 pr-4 text-base text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-white focus:ring-4 focus:ring-white/20"
                        >
                    </div>


                    <!-- Search Button -->
                    <button
                        type="submit"
                        class="h-14 rounded-xl bg-white px-7 text-base font-semibold text-indigo-700 shadow-lg transition hover:bg-blue-50 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-white/30"
                    >
                        <span class="flex items-center justify-center gap-2">
                            <svg class="h-5 w-5"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/>
                            </svg>

                            Search Jobs
                        </span>
                    </button>

                </form>
            </div>
        </div>


        <!-- QUICK STATS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-10">

            <div class="rounded-2xl bg-gradient-to-br from-white to-blue-50 border border-blue-100 p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-600">
                    Active Applications
                </p>

                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-bold text-slate-950">0</p>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                    </div>
                </div>
            </div>


            <div class="rounded-2xl bg-gradient-to-br from-white to-indigo-50 border border-indigo-100 p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-600">
                    Saved Jobs
                </p>

                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-bold text-slate-950">0</p>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18l-6-3-6 3V4Z"/>
                        </svg>
                    </div>
                </div>
            </div>


            <div class="rounded-2xl bg-gradient-to-br from-white to-sky-50 border border-sky-100 p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-600">
                    Profile Views
                </p>

                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-bold text-slate-950">12</p>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                </div>
            </div>


            <div class="rounded-2xl bg-gradient-to-br from-white to-blue-50 border border-blue-100 p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-600">
                    Recommendations
                </p>

                <div class="mt-3 flex items-end justify-between">
                    <p class="text-3xl font-bold text-slate-950">8</p>

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 3 4 14h7l-1 7 9-11h-7l1-7Z"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>


        <!-- FEATURED JOBS -->
        <div class="mb-10">

            <div class="flex items-end justify-between mb-5">
                <div>
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-wide">
                        Opportunities
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-slate-950">
                        Featured Opportunities
                    </h2>

                    <p class="mt-1 text-sm text-slate-600">
                        Jobs that could be a great match for you.
                    </p>
                </div>

                <a href="#" class="hidden sm:block text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                    View all →
                </a>
            </div>


            <div class="space-y-4">

                <!-- Job 1 -->
                <div class="rounded-2xl border border-blue-100 bg-gradient-to-r from-white via-blue-50 to-indigo-50 p-5 sm:p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex-1">

                            <div class="flex items-start gap-4">

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white font-bold">
                                    TF
                                </div>

                                <div>
                                    <h3 class="text-lg font-bold text-slate-950">
                                        Senior Product Designer
                                    </h3>

                                    <p class="mt-1 text-sm font-medium text-indigo-700">
                                        TechFlow Inc.
                                    </p>
                                </div>

                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">Design</span>
                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700">Figma</span>
                                <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-medium text-sky-700">UI/UX</span>
                            </div>

                            <p class="mt-3 text-sm leading-6 text-slate-600">
                                Join a fast-growing team building the future of product design.
                            </p>

                        </div>

                        <div class="sm:text-right">

                            <p class="text-lg font-bold text-slate-950">
                                KES 150K+
                            </p>

                            <p class="text-xs text-slate-500 mb-3">
                                per month
                            </p>

                            <a href="#" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                View Job
                            </a>

                        </div>

                    </div>

                </div>


                <!-- Job 2 -->
                <div class="rounded-2xl border border-indigo-100 bg-gradient-to-r from-white via-indigo-50 to-blue-50 p-5 sm:p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex-1">

                            <div class="flex items-start gap-4">

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-blue-700 text-white font-bold">
                                    DI
                                </div>

                                <div>
                                    <h3 class="text-lg font-bold text-slate-950">
                                        Full Stack Engineer
                                    </h3>

                                    <p class="mt-1 text-sm font-medium text-indigo-700">
                                        Digital Innovations Ltd
                                    </p>
                                </div>

                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700">Backend</span>
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">React</span>
                                <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-medium text-sky-700">Node.js</span>
                            </div>

                            <p class="mt-3 text-sm leading-6 text-slate-600">
                                Build scalable applications and work with a modern engineering team.
                            </p>

                        </div>

                        <div class="sm:text-right">

                            <p class="text-lg font-bold text-slate-950">
                                KES 180K+
                            </p>

                            <p class="text-xs text-slate-500 mb-3">
                                per month
                            </p>

                            <a href="#" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                View Job
                            </a>

                        </div>

                    </div>

                </div>


                <!-- Job 3 -->
                <div class="rounded-2xl border border-sky-100 bg-gradient-to-r from-white via-sky-50 to-blue-50 p-5 sm:p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                        <div class="flex-1">

                            <div class="flex items-start gap-4">

                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-600 to-blue-700 text-white font-bold">
                                    SX
                                </div>

                                <div>
                                    <h3 class="text-lg font-bold text-slate-950">
                                        Product Manager
                                    </h3>

                                    <p class="mt-1 text-sm font-medium text-indigo-700">
                                        StartupXyz
                                    </p>
                                </div>

                            </div>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-medium text-sky-700">Product</span>
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">Strategy</span>
                                <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700">Analytics</span>
                            </div>

                            <p class="mt-3 text-sm leading-6 text-slate-600">
                                Lead product vision and strategy for an innovative SaaS platform.
                            </p>

                        </div>

                        <div class="sm:text-right">

                            <p class="text-lg font-bold text-slate-950">
                                KES 200K+
                            </p>

                            <p class="text-xs text-slate-500 mb-3">
                                per month
                            </p>

                            <a href="#" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                View Job
                            </a>

                        </div>

                    </div>

                </div>

            </div>
        </div>


        <!-- RECENT ACTIVITY + QUICK LINKS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Recent Activity -->
            <div class="lg:col-span-2">

                <div class="mb-5">
                    <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                        Your account
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-slate-950">
                        Recent Activity
                    </h2>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

                    <div class="flex items-center gap-4 p-5 border-b border-slate-100">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                            ✓
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-950">
                                Application sent to TechFlow Inc.
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                2 days ago
                            </p>
                        </div>

                    </div>


                    <div class="flex items-center gap-4 p-5 border-b border-slate-100">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-indigo-600">
                            ★
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-950">
                                Saved "Full Stack Engineer"
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                1 week ago
                            </p>
                        </div>

                    </div>


                    <div class="flex items-center gap-4 p-5">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                            ✓
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-950">
                                Profile updated with new skills
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                2 weeks ago
                            </p>
                        </div>

                    </div>

                </div>

            </div>


            <!-- Quick Links -->
            <div>

                <div class="mb-5">
                    <p class="text-sm font-semibold uppercase tracking-wide text-indigo-600">
                        Shortcuts
                    </p>

                    <h2 class="mt-1 text-2xl font-bold text-slate-950">
                        Quick Links
                    </h2>
                </div>


                <div class="space-y-3">

                    <a href="#"
                       class="group block rounded-2xl border border-blue-100 bg-gradient-to-br from-white to-blue-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                        <div class="flex items-center justify-between">

                            <div>
                                <h3 class="font-semibold text-slate-950">
                                    Complete Your Profile
                                </h3>

                                <p class="mt-1 text-sm leading-5 text-slate-600">
                                    Add more details to improve your opportunities.
                                </p>
                            </div>

                            <span class="text-indigo-600 transition group-hover:translate-x-1">
                                →
                            </span>

                        </div>

                    </a>


                    <a href="#"
                       class="group block rounded-2xl border border-indigo-100 bg-gradient-to-br from-white to-indigo-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                        <div class="flex items-center justify-between">

                            <div>
                                <h3 class="font-semibold text-slate-950">
                                    Browse All Jobs
                                </h3>

                                <p class="mt-1 text-sm leading-5 text-slate-600">
                                    Explore available opportunities.
                                </p>
                            </div>

                            <span class="text-indigo-600 transition group-hover:translate-x-1">
                                →
                            </span>

                        </div>

                    </a>


                    <a href="#"
                       class="group block rounded-2xl border border-sky-100 bg-gradient-to-br from-white to-sky-50 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                        <div class="flex items-center justify-between">

                            <div>
                                <h3 class="font-semibold text-slate-950">
                                    Settings & Preferences
                                </h3>

                                <p class="mt-1 text-sm leading-5 text-slate-600">
                                    Manage your account and preferences.
                                </p>
                            </div>

                            <span class="text-indigo-600 transition group-hover:translate-x-1">
                                →
                            </span>

                        </div>

                    </a>

                </div>

            </div>

        </div>

    </div>
</div>


<script>
function dashboardModule() {
    return {
        activeStatTab: 'applications',
        showJobFilters: false,

        init() {
            console.log('Dashboard initialized with Alpine.js');
        },

        toggleFilters() {
            this.showJobFilters = !this.showJobFilters;
        }
    };
}
</script>
@endsection
