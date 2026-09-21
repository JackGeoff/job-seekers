@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-12 sm:py-16 lg:py-20">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/80"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @error('package')
                <div class="mx-auto mb-6 max-w-3xl rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">{{ $message }}</div>
            @enderror
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">Employer plans</p>
                <h1 class="mt-3 text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">Hire Better. Hire Faster.</h1>
                <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg">Post your vacancies on Jobseekers.co.ke and connect with candidates actively looking for opportunities.</p>
            </div>

            @if ($enterpriseEnquiry)
                <div class="mx-auto mt-10 max-w-2xl rounded-3xl border border-brand-200 bg-white p-7 text-center shadow-xl shadow-brand-900/10 sm:p-10" role="status">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-100 text-2xl text-accent-600">★</span>
                    <h2 class="mt-5 text-2xl font-semibold text-slate-950">Let’s build the right hiring plan</h2>
                    <p class="mx-auto mt-3 max-w-lg text-sm leading-6 text-slate-600">Enterprise support is tailored to your hiring volume, reporting needs and recruitment workflow.</p>
                    <a href="mailto:{{ config('mail.from.address') }}?subject={{ rawurlencode('Enterprise employer enquiry') }}" class="brand-btn accent-btn mt-7">Talk to Us <span class="ml-2" aria-hidden="true">→</span></a>
                    <a href="{{ route('employer.pricing') }}" class="mt-4 block text-sm font-semibold text-brand-700 hover:text-brand-900">Back to plans</a>
                </div>
            @else
                <div class="mt-10">
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-4">
                        @foreach ($packages as $packageKey => $package)
                            <article class="group flex h-full flex-col rounded-3xl border {{ $packageKey === 'business' ? 'border-accent-400 ring-2 ring-accent-400/20' : 'border-brand-100' }} bg-white p-6 shadow-lg shadow-brand-900/5 transition duration-200 hover:-translate-y-1 hover:shadow-xl hover:shadow-brand-900/10 sm:p-7">
                                <div class="mb-4 h-6">
                            @if ($packageKey === 'business')
                                    <span class="inline-flex w-fit rounded-full bg-accent-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-accent-700">Most popular</span>
                            @endif
                                </div>
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-semibold text-slate-950">{{ $package['name'] }}</h2>
                                    <p class="mt-2 text-sm text-slate-500">Best for {{ $package['best_for'] }}</p>
                                </div>
                                <span class="rounded-xl bg-brand-50 px-2.5 py-1 text-xs font-bold uppercase tracking-wide text-brand-700">{{ $package['jobs'] }}</span>
                            </div>
                            <p class="mt-7 text-3xl font-semibold tracking-tight text-brand-900">{{ $package['price'] }}</p>
                            <p class="mt-1 text-sm font-medium text-slate-500">{{ $package['validity'] }}</p>
                            <div class="mt-6 border-t border-slate-100 pt-6">
                                <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Includes</p>
                                <ul class="mt-4 space-y-3">
                                    @foreach ($package['features'] as $feature)
                                        <li class="flex gap-2.5 text-sm leading-5 text-slate-600"><span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-xs font-bold text-emerald-700" aria-hidden="true">✓</span><span>{{ $feature }}</span></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="mt-auto pt-8">
                                @if ($packageKey === 'enterprise')
                                    <a href="{{ route('employer.enterprise') }}" class="brand-btn w-full">Talk to Us <span class="ml-2" aria-hidden="true">→</span></a>
                                @else
                                    <form method="POST" action="{{ route('employer.pricing.select') }}">
                                        @csrf
                                        <input type="hidden" name="package" value="{{ $packageKey }}">
                                        <button type="submit" class="brand-btn w-full">Choose {{ $package['name'] }} <span class="ml-2" aria-hidden="true">→</span></button>
                                    </form>
                                @endif
                            </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
