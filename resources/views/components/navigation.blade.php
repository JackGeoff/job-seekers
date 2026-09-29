```php
<nav
    class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 shadow-sm backdrop-blur-xl"
    x-data="{ mobileOpen: false, userMenuOpen: false }"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- ================================
             MAIN NAVIGATION BAR
        ================================= --}}
        <div class="flex min-h-[72px] items-center justify-between gap-4">

            {{-- ================================
                 LOGO
            ================================= --}}
            <a
                href="{{ route('home') }}"
                class="flex shrink-0 items-center rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
            >
                <img
                    src="{{ asset('images/jobseekers-logo.png') }}"
                    alt="Job Seekers"
                    class="brand-logo"
                >
            </a>


            {{-- ================================
                 DESKTOP NAVIGATION
            ================================= --}}
            @auth

                <div class="hidden flex-1 items-center justify-center lg:flex">

                    {{-- =========================
                         CANDIDATE NAVIGATION
                    ========================== --}}
                    @if (Auth::user()->account_type === 'candidate')

                        <div class="flex items-center gap-1 rounded-xl bg-slate-50 p-1">

                            <a
                                href="{{ route('candidate.dashboard') }}"
                                class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-white hover:text-brand-600 hover:shadow-sm"
                            >
                                Dashboard
                            </a>

                            <a
                                href="{{ route('jobs.index') }}"
                                class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-white hover:text-brand-600 hover:shadow-sm"
                            >
                                Find Jobs
                            </a>

                            <a
                                href="{{ route('candidate.applications.index') }}"
                                class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-white hover:text-brand-600 hover:shadow-sm"
                            >
                                My Applications
                            </a>

                            <a
                                href="{{ route('candidate.profile') }}"
                                class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-white hover:text-brand-600 hover:shadow-sm"
                            >
                                My Profile
                            </a>

                        </div>


                    {{-- =========================
                         EMPLOYER NAVIGATION
                    ========================== --}}
                    @elseif (Auth::user()->account_type === 'employer')

                        <div class="flex items-center gap-1 rounded-xl bg-slate-50 p-1">

                            <a
                                href="{{ route('employer.dashboard') }}"
                                class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-white hover:text-brand-600 hover:shadow-sm"
                            >
                                Dashboard
                            </a>

                            <a
                                href="{{ route('employer.pricing') }}"
                                class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-white hover:text-brand-600 hover:shadow-sm"
                            >
                                Pricing
                            </a>

                            <a
                                href="{{ route('employer.jobs.create') }}"
                                class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-brand-700 hover:shadow-md"
                            >
                                + Post a Job
                            </a>

                            <a
                                href="{{ route('employer.jobs.index') }}"
                                class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-white hover:text-brand-600 hover:shadow-sm"
                            >
                                Jobs
                            </a>

                            <a
                                href="{{ route('employer.applications.index') }}"
                                class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-white hover:text-brand-600 hover:shadow-sm"
                            >
                                Applications
                            </a>

                            <a
                                href="{{ route('employer.profile') }}"
                                class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-white hover:text-brand-600 hover:shadow-sm"
                            >
                                Company Profile
                            </a>

                        </div>

                    @endif

                </div>

            @endauth


            {{-- ================================
                 RIGHT SIDE
            ================================= --}}
            <div class="flex shrink-0 items-center gap-2">

                @auth

                    {{-- =========================
                         DESKTOP ACCOUNT MENU
                    ========================== --}}
                    <div
                        class="relative hidden lg:block"
                        @click.outside="userMenuOpen = false"
                    >

                        <button
                            @click="userMenuOpen = !userMenuOpen"
                            type="button"
                            class="group flex items-center gap-2 rounded-xl border border-transparent px-3 py-2 transition-all duration-200 hover:border-slate-200 hover:bg-slate-50"
                            aria-label="Open account menu"
                            :aria-expanded="userMenuOpen"
                        >

                            {{-- User Initial --}}
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700"
                            >
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>

                            {{-- User Name --}}
                            <span class="max-w-[130px] truncate text-sm font-semibold text-slate-700">
                                {{ Auth::user()->name }}
                            </span>

                            {{-- Clean Chevron --}}
                            <svg
                                class="h-4 w-4 text-slate-400 transition-transform duration-200"
                                :class="{ 'rotate-180': userMenuOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 9l6 6 6-6"
                                />
                            </svg>

                        </button>


                        {{-- =========================
                             ACCOUNT DROPDOWN
                        ========================== --}}
                        <div
                            x-show="userMenuOpen"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute right-0 mt-2 w-60 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10"
                            style="display: none;"
                        >

                            {{-- Account Header --}}
                            <div class="border-b border-slate-100 px-4 py-3">
                                <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                                    Account
                                </p>

                                <p class="mt-1 truncate text-sm font-semibold text-slate-800">
                                    {{ Auth::user()->name }}
                                </p>
                            </div>


                            {{-- Candidate Dropdown --}}
                            @if (Auth::user()->account_type === 'candidate')

                                <div class="p-2">

                                    <a
                                        href="{{ route('candidate.dashboard') }}"
                                        class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                                    >
                                        Dashboard
                                    </a>

                                    <a
                                        href="{{ route('candidate.applications.index') }}"
                                        class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                                    >
                                        My Applications
                                    </a>

                                    <a
                                        href="{{ route('candidate.profile') }}"
                                        class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                                    >
                                        My Profile
                                    </a>

                                </div>


                            {{-- Employer Dropdown --}}
                            @elseif (Auth::user()->account_type === 'employer')

                                <div class="p-2">

                                    <a
                                        href="{{ route('employer.dashboard') }}"
                                        class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                                    >
                                        Dashboard
                                    </a>

                                    <a
                                        href="{{ route('employer.pricing') }}"
                                        class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                                    >
                                        Pricing
                                    </a>

                                    <a
                                        href="{{ route('employer.jobs.index') }}"
                                        class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                                    >
                                        My Jobs
                                    </a>

                                    <a
                                        href="{{ route('employer.applications.index') }}"
                                        class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                                    >
                                        Applications
                                    </a>

                                    <a
                                        href="{{ route('employer.profile') }}"
                                        class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                                    >
                                        Company Profile
                                    </a>

                                </div>

                            @endif


                            {{-- Divider --}}
                            <div class="border-t border-slate-100"></div>


                            {{-- Logout --}}
                            <div class="p-2">

                                <form method="POST" action="{{ route('logout') }}">

                                    @csrf

                                    <button
                                        type="submit"
                                        class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium text-red-600 transition hover:bg-red-50"
                                    >
                                        Sign out
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>


                    {{-- =========================
                         MOBILE MENU BUTTON
                    ========================== --}}
                    <button
                        @click="mobileOpen = !mobileOpen"
                        type="button"
                        class="rounded-xl border border-slate-200 p-2.5 text-slate-600 transition hover:bg-slate-50 hover:text-brand-600 lg:hidden"
                        aria-label="Open navigation menu"
                        :aria-expanded="mobileOpen"
                    >

                        <svg
                            x-show="!mobileOpen"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>

                        <svg
                            x-show="mobileOpen"
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                            style="display: none;"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>

                    </button>


                @else

                    {{-- =========================
                         GUEST NAVIGATION
                    ========================== --}}
                    <div class="flex items-center gap-2">

                        <a
                            href="{{ route('login') }}"
                            class="rounded-xl px-4 py-2.5 text-sm font-semibold text-brand-700 transition hover:bg-brand-50"
                        >
                            Sign in
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="brand-btn min-h-10 rounded-xl px-5 py-2.5 text-sm font-semibold"
                        >
                            Get Started
                        </a>

                    </div>

                @endauth

            </div>

        </div>


        {{-- ================================
             MOBILE NAVIGATION
        ================================= --}}
        @auth

            <div
                x-show="mobileOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="border-t border-slate-200 lg:hidden"
                style="display: none;"
            >

                <div class="space-y-1 px-2 py-4">

                    {{-- =========================
                         CANDIDATE MOBILE
                    ========================== --}}
                    @if (Auth::user()->account_type === 'candidate')

                        <a
                            href="{{ route('candidate.dashboard') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                        >
                            Dashboard
                        </a>

                        <a
                            href="{{ route('jobs.index') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                        >
                            Find Jobs
                        </a>

                        <a
                            href="{{ route('candidate.applications.index') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                        >
                            My Applications
                        </a>

                        <a
                            href="{{ route('candidate.profile') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                        >
                            My Profile
                        </a>


                    {{-- =========================
                         EMPLOYER MOBILE
                    ========================== --}}
                    @elseif (Auth::user()->account_type === 'employer')

                        <a
                            href="{{ route('employer.dashboard') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                        >
                            Dashboard
                        </a>

                        <a
                            href="{{ route('employer.pricing') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                        >
                            Pricing
                        </a>

                        <a
                            href="{{ route('employer.jobs.create') }}"
                            class="block rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700"
                        >
                            + Post a Job
                        </a>

                        <a
                            href="{{ route('employer.jobs.index') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                        >
                            Jobs
                        </a>

                        <a
                            href="{{ route('employer.applications.index') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                        >
                            Applications
                        </a>

                        <a
                            href="{{ route('employer.profile') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-brand-600"
                        >
                            Company Profile
                        </a>

                    @endif


                    {{-- =========================
                         MOBILE LOGOUT
                    ========================== --}}
                    <div class="mt-3 border-t border-slate-200 pt-3">

                        <form method="POST" action="{{ route('logout') }}">

                            @csrf

                            <button
                                type="submit"
                                class="w-full rounded-xl px-4 py-3 text-left text-sm font-medium text-red-600 transition hover:bg-red-50"
                            >
                                Sign out
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        @endauth

    </div>
</nav>
```
