@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-10 sm:py-14">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/75 via-white to-accent-50/70"></div>
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">Order pending</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $package['name'] }} plan</h1>
                <p class="mt-3 text-base leading-7 text-slate-600">Your checkout is recorded as pending. Payment processing is not connected, and this order has not activated a subscription or posting credits.</p>

                <dl class="mt-7 grid gap-4 rounded-xl bg-slate-50 p-5 sm:grid-cols-2">
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Order reference</dt><dd class="mt-1 break-all font-mono text-sm font-semibold text-slate-950">{{ $order->order_reference }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</dt><dd class="mt-1 font-semibold capitalize text-amber-700">{{ str_replace('_', ' ', $order->status) }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</dt><dd class="mt-1 font-semibold text-slate-950">KES {{ number_format($order->amount) }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Payment method</dt><dd class="mt-1 font-semibold text-slate-950">{{ ['mpesa' => 'M-Pesa', 'card' => 'Visa / Mastercard', 'bank_transfer' => 'Bank transfer'][$order->payment_method] ?? $order->payment_method }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Job posting allowance</dt><dd class="mt-1 font-semibold text-slate-950">{{ $order->job_allowance }} jobs</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Plan validity</dt><dd class="mt-1 font-semibold text-slate-950">{{ $package['validity'] }}</dd></div>
                </dl>

                @if ($order->payment_method === 'bank_transfer')
                    <div class="mt-6 rounded-xl border border-brand-200 bg-brand-50 p-5">
                        <h2 class="font-semibold text-slate-950">Bank transfer details</h2>
                        <dl class="mt-3 space-y-2 text-sm text-slate-700">
                            <div class="flex flex-wrap justify-between gap-2"><dt>NCBA account</dt><dd class="font-semibold">1003249278</dd></div>
                            <div class="flex flex-wrap justify-between gap-2"><dt>I&amp;M account</dt><dd class="font-semibold">0020 6092 5461 50</dd></div>
                        </dl>
                        <p class="mt-4 text-sm leading-6 text-slate-600">Transfers require manual verification. Do not consider this order paid or active until verified.</p>
                    </div>
                @elseif ($order->payment_method === 'mpesa')
                    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-5 text-sm leading-6 text-slate-600">M-Pesa integration is not connected yet. No STK push or payment request was sent.</div>
                @else
                    <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-5 text-sm leading-6 text-slate-600">Card processing is not connected yet. No card details were collected and no charge was made.</div>
                @endif

                <a href="{{ route('employer.pricing') }}" class="brand-btn accent-btn mt-7">Back to plans</a>
            </div>
        </div>
    </section>
@endsection