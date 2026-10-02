@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-10 sm:py-14">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/75 via-white to-accent-50/70"></div>

        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="mb-8">
                <a href="{{ route('employer.jobs.index') }}"
                   class="text-sm font-semibold text-brand-600 hover:text-brand-700">
                    ← Back to My Jobs
                </a>

                <p class="mt-6 text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">
                    Manage job posting
                </p>

                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                    Edit Job
                </h1>

                <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
                    Update the details of this job posting and control whether candidates can see it.
                </p>
            </div>

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-red-800"
                     role="alert">

                    <p class="font-semibold">
                        Please fix the following errors:
                    </p>

                    <ul class="mt-2 list-inside list-disc text-sm leading-6">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST"
                  action="{{ route('employer.jobs.update', $job) }}"
                  class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-brand-900/5 sm:p-8">

                @csrf
                @method('PUT')

                {{-- Job Information --}}
                <div>
                    <div>
                        <p class="text-lg font-semibold text-slate-950">
                            Job Information
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Update the main information about this position.
                        </p>
                    </div>

                    <div class="mt-6 grid gap-5">

                        {{-- Title --}}
                        <div>
                            <label for="title"
                                   class="mb-2 block text-sm font-semibold text-slate-800">
                                Job Title
                                <span class="text-red-600">*</span>
                            </label>

                            <input
                                id="title"
                                name="title"
                                type="text"
                                value="{{ old('title', $job->title) }}"
                                required
                                maxlength="255"
                                class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('title') border-red-500 @else border-slate-200 @enderror"
                            >

                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        @include('employer.jobs.partials.category-select', [
                            'categoryGroups' => $categoryGroups,
                            'selectedCategory' => old('category', $job->category),
                            'legacyCategory' => $job->category,
                        ])

                        {{-- Description --}}
                        <div>

                            @php
                                $descriptionSanitizer = app(\App\Support\JobDescriptionSanitizer::class);
                                $descriptionHtml = $descriptionSanitizer->sanitize(old('description', $job->description));
                                $descriptionText = $descriptionSanitizer->plainText($descriptionHtml);
                            @endphp

                            <label for="description"
                                id="description-label"
                                   class="mb-2 block text-sm font-semibold text-slate-800">
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
                                class="auth-input w-full rounded-xl border bg-white px-4 py-3 text-slate-950 outline-none transition @error('description') border-red-500 @else border-slate-200 @enderror"
                            >{{ $descriptionText }}</textarea>
                            </div>

                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Location --}}
                <div class="mt-10 border-t border-slate-100 pt-8">

                    <div>
                        <p class="text-lg font-semibold text-slate-950">
                            Location & Employment
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Update where and how candidates will work.
                        </p>
                    </div>

                    <div class="mt-6 grid gap-5 sm:grid-cols-2">

                        {{-- Location --}}
                        <div>
                            <label for="location"
                                   class="mb-2 block text-sm font-semibold text-slate-800">
                                Location
                                <span class="text-red-600">*</span>
                            </label>

                            <input
                                id="location"
                                name="location"
                                type="text"
                                value="{{ old('location', $job->location) }}"
                                required
                                maxlength="255"
                                class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('location') border-red-500 @else border-slate-200 @enderror"
                            >

                            @error('location')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Employment --}}
                        <div>
                            <label for="employment_type"
                                   class="mb-2 block text-sm font-semibold text-slate-800">
                                Employment Type
                                <span class="text-red-600">*</span>
                            </label>

                            <select
                                id="employment_type"
                                name="employment_type"
                                required
                                class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"
                            >
                                <option value="">Select employment type</option>

                                <option value="full-time"
                                    @selected(old('employment_type', $job->employment_type) === 'full-time')>
                                    Full-time
                                </option>

                                <option value="part-time"
                                    @selected(old('employment_type', $job->employment_type) === 'part-time')>
                                    Part-time
                                </option>

                                <option value="contract"
                                    @selected(old('employment_type', $job->employment_type) === 'contract')>
                                    Contract
                                </option>

                                <option value="temporary"
                                    @selected(old('employment_type', $job->employment_type) === 'temporary')>
                                    Temporary
                                </option>

                                <option value="internship"
                                    @selected(old('employment_type', $job->employment_type) === 'internship')>
                                    Internship
                                </option>
                            </select>

                            @error('employment_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Compensation --}}
                <div class="mt-10 border-t border-slate-100 pt-8">

                    <div>
                        <p class="text-lg font-semibold text-slate-950">
                            Compensation
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Update the salary information.
                        </p>
                    </div>

                    <div class="mt-6 grid gap-5 sm:grid-cols-3">

                        {{-- Currency --}}
                        <div>
                            <label for="salary_currency"
                                   class="mb-2 block text-sm font-semibold text-slate-800">
                                Currency
                                <span class="text-red-600">*</span>
                            </label>

                            <select
                                id="salary_currency"
                                name="salary_currency"
                                required
                                class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"
                            >
                                <option value="KES"
                                    @selected(old('salary_currency', $job->salary_currency) === 'KES')>
                                    KES
                                </option>

                                <option value="USD"
                                    @selected(old('salary_currency', $job->salary_currency) === 'USD')>
                                    USD
                                </option>

                                <option value="EUR"
                                    @selected(old('salary_currency', $job->salary_currency) === 'EUR')>
                                    EUR
                                </option>

                                <option value="GBP"
                                    @selected(old('salary_currency', $job->salary_currency) === 'GBP')>
                                    GBP
                                </option>
                            </select>

                            @error('salary_currency')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Minimum --}}
                        <div>
                            <label for="salary_min"
                                   class="mb-2 block text-sm font-semibold text-slate-800">
                                Minimum Salary
                            </label>

                            <input
                                id="salary_min"
                                name="salary_min"
                                type="number"
                                min="0"
                                step="0.01"
                                value="{{ old('salary_min', $job->salary_min) }}"
                                class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"
                            >

                            @error('salary_min')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Maximum --}}
                        <div>
                            <label for="salary_max"
                                   class="mb-2 block text-sm font-semibold text-slate-800">
                                Maximum Salary
                            </label>

                            <input
                                id="salary_max"
                                name="salary_max"
                                type="number"
                                min="0"
                                step="0.01"
                                value="{{ old('salary_max', $job->salary_max) }}"
                                class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"
                            >

                            @error('salary_max')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
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
                    </div>

                    <div class="mt-6 max-w-sm">
                        <label for="application_deadline"
                               class="mb-2 block text-sm font-semibold text-slate-800">
                            Deadline
                        </label>

                        <input
                            id="application_deadline"
                            name="application_deadline"
                            type="date"
                            value="{{ old('application_deadline', optional($job->application_deadline)->format('Y-m-d')) }}"
                            class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"
                        >

                        @error('application_deadline')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
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
                        <label for="external_application_url"
                               class="mb-2 block text-sm font-semibold text-slate-800">
                            External Application URL
                        </label>

                        <input
                            id="external_application_url"
                            name="external_application_url"
                            type="url"
                            value="{{ old('external_application_url', $job->external_application_url) }}"
                            maxlength="2048"
                            placeholder="https://example.com/apply"
                            class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('external_application_url') border-red-500 @else border-slate-200 @enderror"
                        >

                        @error('external_application_url')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Status --}}
                <div class="mt-10 border-t border-slate-100 pt-8">

                    <div>
                        <p class="text-lg font-semibold text-slate-950">
                            Job Status
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Published jobs are visible to candidates. Draft jobs remain private.
                        </p>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-3">

                        {{-- Draft --}}
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="status"
                                value="draft"
                                class="peer sr-only"
                                @checked(old('status', $job->status) === 'draft')
                            >

                            <div class="rounded-2xl border border-slate-200 p-4 text-slate-900 transition hover:border-accent-600 hover:bg-accent-500 hover:text-white peer-checked:border-accent-600 peer-checked:bg-accent-500 peer-checked:text-white">
                                <p class="font-semibold">
                                    Draft
                                </p>

                                <p class="mt-1 text-sm opacity-80">
                                    Keep private.
                                </p>
                            </div>
                        </label>

                        {{-- Published --}}
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="status"
                                value="published"
                                class="peer sr-only"
                                @checked(old('status', $job->status) === 'published')
                            >

                            <div class="rounded-2xl border border-slate-200 p-4 text-slate-900 transition hover:border-accent-600 hover:bg-accent-500 hover:text-white peer-checked:border-accent-600 peer-checked:bg-accent-500 peer-checked:text-white">
                                <p class="font-semibold">
                                    Published
                                </p>

                                <p class="mt-1 text-sm opacity-80">
                                    Visible to candidates.
                                </p>
                            </div>
                        </label>

                        {{-- Closed --}}
                        <label class="cursor-pointer">
                            <input
                                type="radio"
                                name="status"
                                value="closed"
                                class="peer sr-only"
                                @checked(old('status', $job->status) === 'closed')
                            >

                            <div class="rounded-2xl border border-slate-200 p-4 text-slate-900 transition hover:border-accent-600 hover:bg-accent-500 hover:text-white peer-checked:border-accent-600 peer-checked:bg-accent-500 peer-checked:text-white">
                                <p class="font-semibold">
                                    Closed
                                </p>

                                <p class="mt-1 text-sm opacity-80">
                                    No longer accepting applications.
                                </p>
                            </div>
                        </label>

                    </div>

                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Actions --}}
                <div class="mt-10 flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-between">

                    <a href="{{ route('employer.jobs.index') }}"
                       class="rounded-xl px-5 py-3 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="brand-btn accent-btn w-full sm:w-auto"
                    >
                        Save Changes
                    </button>

                </div>

            </form>

        </div>
    </section>
@endsection
