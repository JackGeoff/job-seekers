@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-8 sm:py-12">

        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/60"></div>

        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

            <a
                href="{{ route('jobs.show', $job) }}"
                class="text-sm font-semibold text-brand-600 hover:text-brand-700"
            >
                ← Back to Job
            </a>

            <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-brand-900/5 sm:p-8">

                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">
                        Job Application
                    </p>

                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">
                        Apply for {{ $job->title }}
                    </h1>

                    <p class="mt-2 text-base text-slate-600">
                        {{ $job->employerProfile->company_name }}
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4">
                        <p class="text-sm font-semibold text-red-800">
                            Please correct the following:
                        </p>

                        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('candidate.jobs.apply', $job) }}"
                    enctype="multipart/form-data"
                    class="mt-8 space-y-6"
                >
                    @csrf

                    <div>
                        <label
                            for="full_name"
                            class="block text-sm font-semibold text-slate-900"
                        >
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="{{ old('full_name', $candidateProfile?->full_name ?? auth()->user()->name) }}"
                            required
                            class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                    </div>

                    <div>
                        <label
                            for="phone"
                            class="block text-sm font-semibold text-slate-900"
                        >
                            Phone Number
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $candidateProfile?->phone ?? auth()->user()->phone) }}"
                            required
                            class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-3 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                    </div>

                    <div>
                        <label
                            for="email"
                            class="block text-sm font-semibold text-slate-900"
                        >
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', auth()->user()->email) }}"
                            required
                            class="mt-2 block w-full rounded-xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                    </div>

                    <div>
                        <label
                            for="cv"
                            class="block text-sm font-semibold text-slate-900"
                        >
                            CV / Resume
                        </label>

                        <p class="mt-1 text-sm text-slate-500">
                            Upload your CV in PDF, DOC, or DOCX format. Maximum size: 5MB.
                        </p>

                        <input
                            type="file"
                            id="cv"
                            name="cv"
                            accept=".pdf,.doc,.docx"
                            required
                            class="mt-3 block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700"
                        >
                    </div>

                    <div class="border-t border-slate-100 pt-6">

                        <button
                            type="submit"
                            class="brand-btn accent-btn w-full sm:w-auto"
                        >
                            Send Application
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </section>
@endsection
