<nav
    class="sticky top-0 z-50 border-b border-brand-100 bg-white/95 shadow-sm shadow-brand-900/5 backdrop-blur"
    x-data="{ mobileOpen: false, userMenuOpen: false }"
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex h-16 items-center justify-between">

            {{-- Logo --}}
            <a
                href="{{ route('home') }}"
                class="flex items-center rounded-lg focus-visible:outline-brand-500"
            >
                <img
                    src="{{ asset('images/jobseekers-logo.png') }}"
                    alt="Job Seekers"
                    class="brand-logo"
                >
            </a>

            {{-- Desktop Navigation --}}
            @auth

                <div class="hidden items-center gap-7 md:flex">

                    @if (Auth::user()->account_type === 'candidate')

                        <a
                            href="{{ route('candidate.dashboard') }}"
                            class="text-sm font-medium text-slate-600 transition-colors hover:text-brand-600"
                        >
                            Dashboard
                        </a>

                        <a
                            href="{{ route('jobs.index') }}"
                            class="text-sm font-medium text-slate-600 transition-colors hover:text-brand-600"
                        >
                            Find Jobs
                        </a>

                        <a
                            href="{{ route('candidate.applications.index') }}"
                            class="text-sm font-medium text-slate-600 transition-colors hover:text-brand-600"
                        >
                            My Applications
                        </a>

                        <a
                            href="{{ route('candidate.profile') }}"
                            class="text-sm font-medium text-slate-600 transition-colors hover:text-brand-600"
                        >
                            My Profile
                        </a>

                    @elseif (Auth::user()->account_type === 'employer')

                        <a
                            href="{{ route('employer.dashboard') }}"
                            class="text-sm font-medium text-slate-600 transition-colors hover:text-brand-600"
                        >
                            Dashboard
                        </a>

                        <a
                            href="{{ route('employer.jobs.create') }}"
                            class="text-sm font-medium text-slate-600 transition-colors hover:text-brand-600"
                        >
                            Post a Job
                        </a>

                        <a
                            href="{{ route('employer.jobs.index') }}"
                            class="text-sm font-medium text-slate-600 transition-colors hover:text-brand-600"
                        >
                            Jobs
                        </a>

                        <a
                            href="{{ route('employer.applications.index') }}"
                            class="text-sm font-medium text-slate-600 transition-colors hover:text-brand-600"
                        >
                            Applications
                        </a>

                        <a
                            href="{{ route('employer.profile') }}"
                            class="text-sm font-medium text-slate-600 transition-colors hover:text-brand-600"
                        >
                            Company Profile
                        </a>

                    @endif

                </div>

            @endauth

            {{-- Right Side --}}
            <div class="flex items-center gap-4">

                @auth

                    {{-- Desktop Account Menu --}}
                    <div
                        class="relative hidden md:block"
                        @click.outside="userMenuOpen = false"
                    >

                        <button
                            @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center gap-2 rounded-lg px-3 py-2 transition hover:bg-neutral-100"
                        >

                            <span class="text-sm font-medium text-neutral-700">
                                {{ Auth::user()->name }}
                            </span>

                            <svg
                                class="h-4 w-4 text-neutral-600 transition"
                                :class="{ 'rotate-180': userMenuOpen }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3"
                                />
                            </svg>

                        </button>

                        <div
                            x-show="userMenuOpen"
                            x-transition
                            class="absolute right-0 mt-2 w-48 rounded-lg border border-neutral-200 bg-white shadow-lg"
                        >

                            @if (Auth::user()->account_type === 'candidate')

                                <a
                                    href="{{ route('candidate.dashboard') }}"
                                    class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50"
                                >
                                    Dashboard
                                </a>

                                <a
                                    href="{{ route('candidate.applications.index') }}"
                                    class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50"
                                >
                                    My Applications
                                </a>

                                <a
                                    href="{{ route('candidate.profile') }}"
                                    class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50"
                                >
                                    My Profile
                                </a>

                            @elseif (Auth::user()->account_type === 'employer')

                                <a
                                    href="{{ route('employer.dashboard') }}"
                                    class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50"
                                >
                                    Dashboard
                                </a>

                                <a
                                    href="{{ route('employer.jobs.index') }}"
                                    class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50"
                                >
                                    My Jobs
                                </a>

                                <a
                                    href="{{ route('employer.applications.index') }}"
                                    class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50"
                                >
                                    Applications
                                </a>

                                <a
                                    href="{{ route('employer.profile') }}"
                                    class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50"
                                >
                                    Company Profile
                                </a>

                            @endif

                            <div class="border-t border-neutral-200"></div>

                            <form method="POST" action="{{ route('logout') }}">

                                @csrf

                                <button
                                    type="submit"
                                    class="w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-neutral-50"
                                >
                                    Sign out
                                </button>

                            </form>

                        </div>

                    </div>

                    {{-- Mobile Menu --}}
                    <button
                        @click="mobileOpen = !mobileOpen"
                        class="rounded-lg p-2 hover:bg-neutral-100 md:hidden"
                        aria-label="Open navigation menu"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>

                @else

                    <a
                        href="{{ route('login') }}"
                        class="rounded-xl px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50"
                    >
                        Sign in
                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="brand-btn min-h-10 px-4 py-2 text-sm"
                    >
                        Get Started
                    </a>

                @endauth

            </div>

        </div>

        {{-- Mobile Navigation --}}
        @auth

            <div
                x-show="mobileOpen"
                x-transition
                class="border-t border-neutral-200 md:hidden"
            >

                <div class="space-y-2 px-4 py-4">

                    @if (Auth::user()->account_type === 'candidate')

                        <a
                            href="{{ route('candidate.dashboard') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                        >
                            Dashboard
                        </a>

                        <a
                            href="{{ route('jobs.index') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                        >
                            Find Jobs
                        </a>

                        <a
                            href="{{ route('candidate.applications.index') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                        >
                            My Applications
                        </a>

                        <a
                            href="{{ route('candidate.profile') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                        >
                            My Profile
                        </a>

                    @elseif (Auth::user()->account_type === 'employer')

                        <a
                            href="{{ route('employer.dashboard') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                        >
                            Dashboard
                        </a>

                        <a
                            href="{{ route('employer.jobs.create') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                        >
                            Post a Job
                        </a>

                        <a
                            href="{{ route('employer.jobs.index') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                        >
                            Jobs
                        </a>

                        <a
                            href="{{ route('employer.applications.index') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                        >
                            Applications
                        </a>

                        <a
                            href="{{ route('employer.profile') }}"
                            class="block rounded-lg px-4 py-3 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                        >
                            Company Profile
                        </a>

                    @endif

                    <div class="border-t border-neutral-200 pt-3">

                        <form method="POST" action="{{ route('logout') }}">

                            @csrf

                            <button
                                type="submit"
                                class="w-full rounded-lg px-4 py-3 text-left text-sm font-medium text-red-600 hover:bg-red-50"
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
