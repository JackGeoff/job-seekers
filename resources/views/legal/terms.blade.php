<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta
        name="description"
        content="Jobseekers.co.ke Terms and Conditions"
    >

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/favicon.png') }}"
    >

    <title>Terms & Conditions | {{ config('app.name', 'JobSeekers') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-w-0 bg-[#fafaff] font-sans text-slate-950 antialiased">

    @include('components.navigation')

    <main>

        <section class="border-b border-indigo-100 bg-white">
            <div class="mx-auto max-w-4xl px-5 py-16 sm:px-8 lg:px-10">

                <p class="text-sm font-bold uppercase tracking-[.12em] text-indigo-600">
                    Legal
                </p>

                <h1 class="mt-3 text-4xl font-extrabold tracking-tight text-slate-950">
                    Terms & Conditions
                </h1>

                <p class="mt-3 text-sm text-slate-500">
                    Last updated: {{ date('F Y') }}
                </p>

            </div>
        </section>

        <section class="py-12 sm:py-16">
            <div class="mx-auto max-w-4xl px-5 sm:px-8 lg:px-10">

                <div class="prose prose-slate max-w-none">

                    <h2>1. Acceptance of Terms</h2>

                    <p>
                        By accessing or using Jobseekers.co.ke, you agree to
                        comply with these Terms & Conditions. If you do not
                        agree with these terms, please do not use the platform.
                    </p>

                    <h2>2. Our Platform</h2>

                    <p>
                        Jobseekers.co.ke provides an online platform that allows
                        jobseekers to discover employment opportunities and
                        employers to publish recruitment opportunities.
                    </p>

                    <h2>3. Jobseekers</h2>

                    <p>
                        Jobseekers are responsible for providing accurate and
                        truthful information in their profiles, CVs and job
                        applications.
                    </p>

                    <h2>4. Employers</h2>

                    <p>
                        Employers are responsible for ensuring that information
                        contained in their job advertisements is accurate,
                        lawful and not misleading.
                    </p>

                    <h2>5. Job Listings</h2>

                    <p>
                        Jobseekers.co.ke does not guarantee employment as a
                        result of using the platform. Employers are responsible
                        for their own recruitment decisions and processes.
                    </p>

                    <h2>6. User Accounts</h2>

                    <p>
                        Users are responsible for maintaining the security of
                        their account credentials and for activity carried out
                        through their accounts.
                    </p>

                    <h2>7. Prohibited Use</h2>

                    <p>
                        Users must not use the platform for fraudulent,
                        unlawful, abusive or misleading activities, including
                        publishing fraudulent job advertisements or submitting
                        false application information.
                    </p>

                    <h2>8. Intellectual Property</h2>

                    <p>
                        Unless otherwise stated, the Jobseekers.co.ke platform,
                        branding, design and original content are protected by
                        applicable intellectual property laws.
                    </p>

                    <h2>9. Changes to the Service</h2>

                    <p>
                        We may update, modify or discontinue features of the
                        platform from time to time.
                    </p>

                    <h2>10. Contact</h2>

                    <p>
                        Questions regarding these Terms & Conditions can be
                        sent to:
                    </p>

                    <p>
                        <a
                            href="mailto:info@jobseekers.co.ke"
                            class="font-semibold text-indigo-600 hover:text-indigo-800"
                        >
                            info@jobseekers.co.ke
                        </a>
                    </p>

                </div>

            </div>
        </section>

    </main>

    @include('components.footer')

</body>
</html>