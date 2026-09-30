<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta
        name="description"
        content="Jobseekers.co.ke Privacy Policy"
    >

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/favicon.png') }}"
    >

    <title>Privacy Policy | {{ config('app.name', 'JobSeekers') }}</title>

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
                    Privacy Policy
                </h1>

                <p class="mt-3 text-sm text-slate-500">
                    Last updated: {{ date('F Y') }}
                </p>

            </div>
        </section>

        <section class="py-12 sm:py-16">
            <div class="mx-auto max-w-4xl px-5 sm:px-8 lg:px-10">

                <div class="prose prose-slate max-w-none">

                    <p>
                        Jobseekers.co.ke respects your privacy and is committed
                        to protecting your personal information.
                    </p>

                    <h2>Information We Collect</h2>

                    <p>
                        When you create an account, apply for a job, post a job,
                        or otherwise use our platform, we may collect information
                        such as your name, email address, phone number, location,
                        professional information, CV and other information you
                        choose to provide.
                    </p>

                    <h2>How We Use Your Information</h2>

                    <p>
                        We use information provided through the platform to
                        operate Jobseekers.co.ke, facilitate job applications,
                        allow employers to manage recruitment activities,
                        communicate with users, improve our services and
                        maintain platform security.
                    </p>

                    <h2>Job Applications</h2>

                    <p>
                        Information submitted as part of a job application may
                        be shared with the relevant employer for recruitment
                        purposes.
                    </p>

                    <h2>Account Security</h2>

                    <p>
                        We take reasonable measures to protect information
                        submitted through the platform. Users are responsible
                        for keeping their account credentials confidential.
                    </p>

                    <h2>Your Rights</h2>

                    <p>
                        Depending on applicable law, you may have rights relating
                        to access, correction, deletion or other processing of
                        your personal information.
                    </p>

                    <h2>Contact Us</h2>

                    <p>
                        For privacy-related questions or requests, contact us at:
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