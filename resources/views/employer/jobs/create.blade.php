@extends('layouts.app')

@section('content')

    <section class="relative overflow-hidden py-10 sm:py-14">

        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/75 via-white to-accent-50/70"></div>

        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            {{-- Job limit notification --}}
            @if (session('job_limit_reached'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                    role="alert"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 shrink-0 text-red-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 9v4m0 4h.01M10.29 3.86l-8.1 14a2 2 0 001.74 3h16.14a2 2 0 001.74-3l-8.1-14a2 2 0 00-3.42 0z"
                        />
                    </svg>

                    <div class="min-w-0 flex-1">
                        <span class="font-semibold">
                            Job posting limit reached.
                        </span>

                        <span class="ml-1">
                            Please upgrade your plan to post another job.
                        </span>

                        <a
                            href="{{ route('employer.pricing') }}"
                            class="ml-2 font-semibold text-red-700 underline underline-offset-2 hover:text-red-900"
                        >
                            Upgrade plan
                        </a>
                    </div>

                    <button
                        type="button"
                        @click="show = false"
                        class="shrink-0 rounded-lg p-1 text-red-500 transition hover:bg-red-100 hover:text-red-700"
                        aria-label="Close notification"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>

                </div>
            @endif


            {{-- Header --}}
            <div class="mb-8">

                <a
                    href="{{ route('employer.jobs.index') }}"
                    class="text-sm font-semibold text-brand-600 hover:text-brand-700"
                >
                    ← Back to My Jobs
                </a>

                <p class="mt-6 text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">
                    New job posting
                </p>

                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                    Post a Job
                </h1>

                <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
                    Give candidates everything they need to understand the role and decide whether it is right for them.
                </p>

            </div>


            {{-- Validation errors --}}
            @if ($errors->any())

                <div
                    class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-red-800"
                    role="alert"
                >

                    <p class="font-semibold">
                        Please fix the following errors:
                    </p>

                    <ul class="mt-2 list-inside list-disc text-sm leading-6">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- Form --}}
            <form
                method="POST"
                action="{{ route('employer.jobs.store') }}"
                class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-brand-900/5 sm:p-8"
            >

                @csrf


                {{-- Basic information --}}
                <div>

                    <div>

                        <p class="text-lg font-semibold text-slate-950">
                            Job Information
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Start with the basics about the position.
                        </p>

                    </div>


                    <div class="mt-6 grid gap-5">

                        {{-- Job title --}}
                        <div>

                            <label
                                for="title"
                                class="mb-2 block text-sm font-semibold text-slate-800"
                            >
                                Job Title
                                <span class="text-red-600">*</span>
                            </label>

                            <input
                                id="title"
                                name="title"
                                type="text"
                                value="{{ old('title') }}"
                                required
                                maxlength="255"
                                placeholder="e.g. Software Developer"
                                class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('title') border-red-500 @else border-slate-200 @enderror"
                            >

                            @error('title')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Category --}}
                        <div>

                            <label
                                for="category"
                                class="mb-2 block text-sm font-semibold text-slate-800"
                            >
                                Category
                                <span class="text-red-600">*</span>
                            </label>

                            <input
                                id="category"
                                name="category"
                                type="text"
                                value="{{ old('category') }}"
                                required
                                maxlength="255"
                                placeholder="e.g. Information Technology"
                                class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('category') border-red-500 @else border-slate-200 @enderror"
                            >

                            @error('category')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Description --}}
                        <div>

                            @php
                                $descriptionSanitizer = app(\App\Support\JobDescriptionSanitizer::class);
                                $descriptionHtml = $descriptionSanitizer->sanitize(old('description'));
                                $descriptionText = $descriptionSanitizer->plainText($descriptionHtml);
                            @endphp

                            <label
                                for="description"
                                id="description-label"
                                class="mb-2 block text-sm font-semibold text-slate-800"
                            >
                                Job Description
                                <span class="text-red-600">*</span>
                            </label>

                            <div data-job-description-editor>
                                <div
                                    data-quill-editor
                                    data-initial-html="{{ $descriptionHtml }}"
                                    aria-label="Job Description editor"
                                    hidden
                                ></div>

                            <textarea
                                id="description"
                                name="description"
                                rows="8"
                                required
                                placeholder="Describe the role, responsibilities, expectations and what success looks like..."
                                class="auth-input w-full rounded-xl border bg-white px-4 py-3 text-slate-950 outline-none transition @error('description') border-red-500 @else border-slate-200 @enderror"
                            >{{ $descriptionText }}</textarea>
                            </div>

                            <p class="mt-2 text-xs text-slate-500">
                                Minimum 50 characters. Use headings, lists and links to make the role easy to scan.
                            </p>

                            @error('description')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- Location and employment --}}
                <div class="mt-10 border-t border-slate-100 pt-8">

                    <div>

                        <p class="text-lg font-semibold text-slate-950">
                            Location & Employment
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Tell candidates where and how they will work.
                        </p>

                    </div>


                    <div class="mt-6 grid gap-5 sm:grid-cols-2">

                        {{-- Location --}}
                        <div>

                            <label
                                for="location"
                                class="mb-2 block text-sm font-semibold text-slate-800"
                            >
                                Location
                                <span class="text-red-600">*</span>
                            </label>

                            <input
                                id="location"
                                name="location"
                                type="text"
                                value="{{ old('location') }}"
                                required
                                maxlength="255"
                                placeholder="e.g. Nairobi, Kenya"
                                class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('location') border-red-500 @else border-slate-200 @enderror"
                            >

                            @error('location')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Employment type --}}
                        <div>

                            <label
                                for="employment_type"
                                class="mb-2 block text-sm font-semibold text-slate-800"
                            >
                                Employment Type
                                <span class="text-red-600">*</span>
                            </label>

                            <select
                                id="employment_type"
                                name="employment_type"
                                required
                                class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"
                            >

                                <option value="">
                                    Select employment type
                                </option>

                                <option
                                    value="full-time"
                                    @selected(old('employment_type') === 'full-time')
                                >
                                    Full-time
                                </option>

                                <option
                                    value="part-time"
                                    @selected(old('employment_type') === 'part-time')
                                >
                                    Part-time
                                </option>

                                <option
                                    value="contract"
                                    @selected(old('employment_type') === 'contract')
                                >
                                    Contract
                                </option>

                                <option
                                    value="temporary"
                                    @selected(old('employment_type') === 'temporary')
                                >
                                    Temporary
                                </option>

                                <option
                                    value="internship"
                                    @selected(old('employment_type') === 'internship')
                                >
                                    Internship
                                </option>

                            </select>

                            @error('employment_type')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- Salary --}}
                <div class="mt-10 border-t border-slate-100 pt-8">

                    <div>

                        <p class="text-lg font-semibold text-slate-950">
                            Compensation
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Salary information is optional but helps attract relevant candidates.
                        </p>

                    </div>


                    <div class="mt-6 grid gap-5 sm:grid-cols-3">

                        {{-- Currency --}}
                        <div>

                            <label
                                for="salary_currency"
                                class="mb-2 block text-sm font-semibold text-slate-800"
                            >
                                Currency
                                <span class="text-red-600">*</span>
                            </label>

                            <select
                                id="salary_currency"
                                name="salary_currency"
                                required
                                class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"
                            >

                                <option
                                    value="KES"
                                    @selected(old('salary_currency', 'KES') === 'KES')
                                >
                                    KES
                                </option>

                                <option
                                    value="USD"
                                    @selected(old('salary_currency') === 'USD')
                                >
                                    USD
                                </option>

                                <option
                                    value="EUR"
                                    @selected(old('salary_currency') === 'EUR')
                                >
                                    EUR
                                </option>

                                <option
                                    value="GBP"
                                    @selected(old('salary_currency') === 'GBP')
                                >
                                    GBP
                                </option>

                            </select>

                            @error('salary_currency')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Minimum --}}
                        <div>

                            <label
                                for="salary_min"
                                class="mb-2 block text-sm font-semibold text-slate-800"
                            >
                                Minimum Salary
                            </label>

                            <input
                                id="salary_min"
                                name="salary_min"
                                type="number"
                                min="0"
                                step="0.01"
                                value="{{ old('salary_min') }}"
                                placeholder="e.g. 50000"
                                class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"
                            >

                            @error('salary_min')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- Maximum --}}
                        <div>

                            <label
                                for="salary_max"
                                class="mb-2 block text-sm font-semibold text-slate-800"
                            >
                                Maximum Salary
                            </label>

                            <input
                                id="salary_max"
                                name="salary_max"
                                type="number"
                                min="0"
                                step="0.01"
                                value="{{ old('salary_max') }}"
                                placeholder="e.g. 80000"
                                class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"
                            >

                            @error('salary_max')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                </div>


                {{-- Deadline --}}
                <div class="mt-10 border-t border-slate-100 pt-8">

                    <div>

                        <p class="text-lg font-semibold text-slate-950">
                            Application Deadline
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Leave blank if the position does not have a fixed deadline.
                        </p>

                    </div>


                    <div class="mt-6 max-w-sm">

                        <label
                            for="application_deadline"
                            class="mb-2 block text-sm font-semibold text-slate-800"
                        >
                            Deadline
                        </label>

                        <input
                            id="application_deadline"
                            name="application_deadline"
                            type="date"
                            min="{{ now()->format('Y-m-d') }}"
                            value="{{ old('application_deadline') }}"
                            class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"
                        >

                        @error('application_deadline')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>


                {{-- External application link --}}
                <div class="mt-10 border-t border-slate-100 pt-8">

                    <div>

                        <p class="text-lg font-semibold text-slate-950">
                            External Application Link (Optional)
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Candidates will be redirected to an external website or application form when they click Apply Now. Leave this field empty to use the platform's internal application process.
                        </p>

                    </div>

                    <div class="mt-6 max-w-2xl">

                        <label
                            for="external_application_url"
                            class="mb-2 block text-sm font-semibold text-slate-800"
                        >
                            External Application URL
                        </label>

                        <input
                            id="external_application_url"
                            name="external_application_url"
                            type="url"
                            value="{{ old('external_application_url') }}"
                            maxlength="2048"
                            placeholder="https://example.com/apply"
                            class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('external_application_url') border-red-500 @else border-slate-200 @enderror"
                        >

                        @error('external_application_url')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>


                {{-- Publishing --}}
                <div class="mt-10 border-t border-slate-100 pt-8">

                    <div>

                        <p class="text-lg font-semibold text-slate-950">
                            Publishing
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Choose whether candidates can see this job immediately.
                        </p>

                    </div>


                    <div class="mt-6 grid gap-3 sm:grid-cols-2">

                        {{-- Draft --}}
                        <label class="cursor-pointer">

                            <input
                                type="radio"
                                name="status"
                                value="draft"
                                class="peer sr-only"
                                @checked(old('status') === 'draft')
                            >

                            <div class="rounded-2xl border border-slate-200 p-4 text-slate-900 transition hover:border-accent-600 hover:bg-accent-500 hover:text-white peer-checked:border-accent-600 peer-checked:bg-accent-500 peer-checked:text-white">

                                <p class="font-semibold">
                                    Save as Draft
                                </p>

                                <p class="mt-1 text-sm opacity-80">
                                    Keep the job private and finish it later.
                                </p>

                            </div>

                        </label>


                        {{-- Publish --}}
                        <label class="cursor-pointer">

                            <input
                                type="radio"
                                name="status"
                                value="published"
                                class="peer sr-only"
                                @checked(old('status', 'published') === 'published')
                            >

                            <div class="rounded-2xl border border-slate-200 p-4 text-slate-900 transition hover:border-accent-600 hover:bg-accent-500 hover:text-white peer-checked:border-accent-600 peer-checked:bg-accent-500 peer-checked:text-white">

                                <p class="font-semibold">
                                    Publish Job
                                </p>

                                <p class="mt-1 text-sm opacity-80">
                                    Make the job visible to candidates.
                                </p>

                            </div>

                        </label>

                    </div>


                    @error('status')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Actions --}}
                <div class="mt-10 flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-between">

                    <a
                        href="{{ route('employer.jobs.index') }}"
                        class="rounded-xl px-5 py-3 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="brand-btn accent-btn w-full sm:w-auto"
                    >
                        Create Job
                    </button>

                </div>

            </form>

        </div>

    </section>

@endsection