@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-10 sm:py-14">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-80 bg-gradient-to-br from-brand-100/75 via-white to-accent-50/70"></div>
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">Employer profile</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Set Up Your Company Profile</h1>
                <p class="mt-3 text-base leading-7 text-slate-600">Tell job seekers about your company and create a profile they can trust.</p>
            </div>
            <div class="surface-card rounded-2xl p-5 sm:p-7">
                <div class="flex items-end justify-between gap-4"><div><p class="text-sm font-semibold text-slate-900">Profile Completion</p><p class="mt-1 text-sm text-slate-500">A complete company profile builds candidate confidence.</p></div><span class="text-lg font-bold text-accent-600">{{ $completion }}%</span></div>
                <div class="mt-4 h-2.5 overflow-hidden rounded-full bg-brand-100" role="progressbar" aria-label="Profile completion" aria-valuenow="{{ $completion }}" aria-valuemin="0" aria-valuemax="100"><div class="h-full rounded-full bg-gradient-to-r from-accent-500 to-accent-600" style="width: {{ $completion }}%"></div></div>
            </div>
            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-5 text-red-800" role="alert"><p class="font-semibold">Please review the highlighted fields.</p><ul class="mt-2 list-inside list-disc text-sm leading-6">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('employer.profile.store') }}" class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-xl shadow-brand-900/5 sm:p-8">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach ([['company_name', 'Company Name', 'text', 'e.g. Acme Ltd'], ['industry', 'Industry', 'text', 'e.g. Technology'], ['location', 'Location', 'text', 'e.g. Nairobi, Kenya'], ['phone', 'Contact Phone', 'tel', '']] as [$field, $label, $type, $placeholder])
                        <div><label for="{{ $field }}" class="mb-2 block text-sm font-semibold text-slate-800">{{ $label }} <span class="text-red-600">*</span></label><input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field, $profile?->{$field} ?? ($field === 'phone' ? auth()->user()->phone : '')) }}" required placeholder="{{ $placeholder }}" class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error($field) border-red-500 @else border-slate-200 @enderror">@error($field)<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    @endforeach
                    <div class="sm:col-span-2"><label for="website" class="mb-2 block text-sm font-semibold text-slate-800">Website</label><input id="website" name="website" type="url" value="{{ old('website', $profile?->website) }}" placeholder="https://example.com" class="auth-input h-12 w-full rounded-xl border bg-white px-4 text-slate-950 outline-none transition @error('website') border-red-500 @else border-slate-200 @enderror">@error('website')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                    <div class="sm:col-span-2"><label for="description" class="mb-2 block text-sm font-semibold text-slate-800">Company Description</label><textarea id="description" name="description" rows="6" placeholder="What does your company do, and what makes it a great place to work?" class="auth-input w-full rounded-xl border bg-white px-4 py-3 text-slate-950 outline-none transition @error('description') border-red-500 @else border-slate-200 @enderror">{{ old('description', $profile?->description) }}</textarea>@error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                </div>
                <div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:items-center sm:justify-between"><p class="text-sm text-slate-500">You can refine your company details whenever you need to.</p><button type="submit" class="brand-btn accent-btn w-full sm:w-auto">Save Company Profile</button></div>
            </form>
        </div>
    </section>
@endsection
