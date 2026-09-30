<footer class="border-t border-brand-100 bg-white/90">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">

        <div class="mb-8 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Brand --}}
            <div>
                <a href="{{ route('home') }}" class="mb-4 flex items-center">
                    <img
                        src="{{ asset('images/jobseekers-logo.png') }}"
                        alt="Job Seekers"
                        class="brand-logo"
                    >
                </a>

                <p class="max-w-xs text-sm leading-6 text-slate-600">
                    Finding the right talent for Kenya's growing job market.
                </p>

                <div class="mt-5">
                    <a
                        href="mailto:services@jobseekers.co.ke"
                        class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-800"
                    >
                        services@jobseekers.co.ke
                    </a>
                </div>
            </div>


            {{-- Product --}}
            <div>
                <h3 class="mb-4 text-sm font-semibold text-neutral-900">
                    Product
                </h3>

                <ul class="space-y-3 text-sm">

                    <li>
                        <a
                            href="{{ route('jobs.index') }}"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Browse Jobs
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('employer.register') }}"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Recruit with Jobseekers
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('pricing') }}"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Pricing
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('blog.index') }}"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Career Blog
                        </a>
                    </li>

                </ul>
            </div>


            {{-- Account --}}
            <div>
                <h3 class="mb-4 text-sm font-semibold text-neutral-900">
                    Account
                </h3>

                <ul class="space-y-3 text-sm">

                    <li>
                        <a
                            href="{{ route('register') }}"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Create Account
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('login') }}"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Sign In
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('jobs.index') }}"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Find Jobs
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('career-guide') }}"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Career Guide
                        </a>
                    </li>

                </ul>
            </div>


            {{-- Legal & Contact --}}
            <div>
                <h3 class="mb-4 text-sm font-semibold text-neutral-900">
                    Legal & Contact
                </h3>

                <ul class="space-y-3 text-sm">

                    <li>
                        <a
                            href="{{ route('privacy') }}"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Privacy Policy
                        </a>
                    </li>

                    <li>
                        <a
                            href="{{ route('terms') }}"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Terms & Conditions
                        </a>
                    </li>

                    <li>
                        <a
                            href="mailto:services@jobseekers.co.ke"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Contact Us
                        </a>
                    </li>

                    <li>
                        <a
                            href="mailto:services@jobseekers.co.ke"
                            class="text-neutral-600 transition hover:text-indigo-600"
                        >
                            Email Support
                        </a>
                    </li>

                </ul>
            </div>

        </div>


        {{-- Bottom section --}}
        <div class="flex flex-col items-center justify-between gap-4 border-t border-neutral-200 pt-8 sm:flex-row">

            <p class="text-center text-sm text-neutral-600 sm:text-left">
                &copy; {{ date('Y') }}
                {{ config('app.name', 'JobSeekers') }}.
                All rights reserved.
            </p>

            <div class="flex items-center gap-6">

                <a
                    href="mailto:services@jobseekers.co.ke"
                    class="text-sm text-neutral-600 transition hover:text-indigo-600"
                    aria-label="Email Jobseekers"
                >
                    Email
                </a>

                <a
                    href="{{ route('blog.index') }}"
                    class="text-sm text-neutral-600 transition hover:text-indigo-600"
                >
                    Blog
                </a>

                <a
                    href="{{ route('privacy') }}"
                    class="text-sm text-neutral-600 transition hover:text-indigo-600"
                >
                    Privacy
                </a>

                <a
                    href="{{ route('terms') }}"
                    class="text-sm text-neutral-600 transition hover:text-indigo-600"
                >
                    Terms
                </a>

            </div>

        </div>

    </div>
</footer>