@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-10 sm:py-14">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-80 bg-gradient-to-br from-brand-100/75 via-white to-accent-50/70"></div>

        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-600">Candidate profile</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Complete Your Profile</h1>
                <p class="mt-3 text-base leading-7 text-slate-600">Build your profile so employers can understand your experience and find you for the right opportunities.</p>
            </div>

            <div class="surface-card rounded-2xl p-5 sm:p-7">
                <div class="flex flex-col gap-3 border-b border-brand-100 pb-6 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Profile Completion</p>
                        <p class="mt-1 text-sm text-slate-500">Add the essentials to make your profile more discoverable.</p>
                    </div>
                    <span class="text-lg font-bold text-accent-600">50%</span>
                </div>
                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-brand-100" role="progressbar" aria-label="Profile completion" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50">
                    <div class="h-full w-1/2 rounded-full bg-gradient-to-r from-brand-600 to-accent-500"></div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-red-800" role="alert">
                    <p class="font-semibold">Please review the highlighted fields.</p>
                    <ul class="mt-2 list-inside list-disc text-sm leading-6">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('candidate.profile.store') }}" class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-xl shadow-brand-900/5 sm:p-8">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="full_name" class="mb-2 block text-sm font-semibold text-slate-800">Full Name <span class="text-red-600">*</span></label>
                        <input id="full_name" name="full_name" type="text" value="{{ old('full_name', $profile?->full_name ?? auth()->user()->name) }}" required autocomplete="name" class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('full_name') border-red-500 @else border-slate-200 @enderror">
                        @error('full_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-semibold text-slate-800">Phone <span class="text-red-600">*</span></label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone', $profile?->phone ?? auth()->user()->phone) }}" required autocomplete="tel" class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('phone') border-red-500 @else border-slate-200 @enderror">
                        @error('phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="location" class="mb-2 block text-sm font-semibold text-slate-800">Location <span class="text-red-600">*</span></label>
                        <input id="location" name="location" type="text" value="{{ old('location', $profile?->location) }}" required autocomplete="address-level2" placeholder="e.g. Nairobi, Kenya" class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('location') border-red-500 @else border-slate-200 @enderror">
                        @error('location') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="job_title" class="mb-2 block text-sm font-semibold text-slate-800">Profession / Job Title <span class="text-red-600">*</span></label>
                        <input id="job_title" name="job_title" type="text" value="{{ old('job_title', $profile?->job_title) }}" required placeholder="e.g. Software Developer" class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('job_title') border-red-500 @else border-slate-200 @enderror">
                        @error('job_title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="skills" class="mb-2 block text-sm font-semibold text-slate-800">Skills</label>
                        <textarea id="skills" name="skills" rows="4" placeholder="e.g. Laravel, project management, Excel" class="auth-input w-full rounded-xl border bg-white px-4 py-3 text-slate-950 outline-none transition @error('skills') border-red-500 @else border-slate-200 @enderror">{{ old('skills', $profile?->skills) }}</textarea>
                        @error('skills') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="education" class="mb-2 block text-sm font-semibold text-slate-800">Education</label>
                        <textarea id="education" name="education" rows="4" placeholder="Share your education or qualifications" class="auth-input w-full rounded-xl border bg-white px-4 py-3 text-slate-950 outline-none transition @error('education') border-red-500 @else border-slate-200 @enderror">{{ old('education', $profile?->education) }}</textarea>
                        @error('education') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="experience" class="mb-2 block text-sm font-semibold text-slate-800">Experience</label>
                        <textarea id="experience" name="experience" rows="4" placeholder="Summarise your relevant experience" class="auth-input w-full rounded-xl border bg-white px-4 py-3 text-slate-950 outline-none transition @error('experience') border-red-500 @else border-slate-200 @enderror">{{ old('experience', $profile?->experience) }}</textarea>
                        @error('experience') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="bio" class="mb-2 block text-sm font-semibold text-slate-800">Bio</label>
                        <textarea id="bio" name="bio" rows="4" placeholder="Tell employers a little about yourself" class="auth-input w-full rounded-xl border bg-white px-4 py-3 text-slate-950 outline-none transition @error('bio') border-red-500 @else border-slate-200 @enderror">{{ old('bio', $profile?->bio) }}</textarea>
                        @error('bio') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">You can return to add or update details at any time.</p>
                    <button type="submit" class="brand-btn w-full sm:w-auto">Save Profile</button>
                </div>
            </form>
        </div>
    </section>
@endsection
