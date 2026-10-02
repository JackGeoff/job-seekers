@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-12 sm:py-16 lg:py-20">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/80"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">Employer plans</p>
                <h1 class="mt-3 text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">Flexible hiring plans</h1>
                <p class="mt-4 text-base leading-7 text-slate-600">Choose the posting capacity and visibility period that fit your hiring needs.</p>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                @foreach ($packages as $packageKey => $package)
                    <a href="{{ $packageKey === 'enterprise' ? 'mailto:' . config('mail.from.address') . '?subject=' . rawurlencode('Enterprise employer enquiry') : route('employer.payment', ['package' => $packageKey]) }}"
                       class="group flex h-full flex-col rounded-2xl border {{ !empty($package['most_popular']) ? 'border-accent-400 ring-2 ring-accent-400/20' : 'border-brand-100' }} bg-white p-6 text-left shadow-sm transition hover:-translate-y-1 hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-600 sm:p-7">
                        <div class="mb-4 flex h-7 items-start">
                            @if (!empty($package['most_popular']))
                                <span class="rounded-full bg-accent-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-accent-700">Most Popular</span>
                            @endif
                        </div>
                        <h2 class="text-xl font-semibold text-slate-950">{{ $package['name'] }}</h2>
                        <p class="mt-2 min-h-10 text-sm text-slate-500">Best for {{ $package['best_for'] }}</p>
                        <p class="mt-5 text-3xl font-semibold tracking-tight text-brand-900">{{ $package['price'] }}</p>
                        <p class="mt-1 text-sm font-medium text-slate-500">{{ $package['jobs'] }} · {{ $package['validity'] }}</p>
                        <ul class="mt-5 flex-1 space-y-2 border-t border-slate-100 pt-5">
                            @foreach ($package['features'] as $feature)
                                <li class="flex gap-2 text-sm leading-5 text-slate-600"><span class="text-emerald-700" aria-hidden="true">✓</span><span>{{ $feature }}</span></li>
                            @endforeach
                        </ul>
                        <span class="brand-btn accent-btn mt-7 w-full justify-center">
                            {{ $packageKey === 'enterprise' ? 'Contact Us' : 'Choose Plan' }}
                            <span class="ml-2" aria-hidden="true">→</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection