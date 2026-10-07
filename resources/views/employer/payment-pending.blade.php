@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-10 sm:py-14">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/75 via-white to-accent-50/70"></div>
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                @if ($order->status === 'paid')
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-emerald-700">Payment confirmed</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $package['name'] }} is active</h1>
                    <p class="mt-3 text-base leading-7 text-slate-600">Paystack verified your payment and your subscription has been activated.</p>
                @elseif ($order->status === 'failed')
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-red-700">Payment not completed</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Retry your {{ $package['name'] }} payment</h1>
                    <p class="mt-3 text-base leading-7 text-slate-600">No subscription or posting credits were activated. You can retry this order below.</p>
                @elseif ($order->status === 'expired')
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-600">Order expired</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Start a new checkout</h1>
                    <p class="mt-3 text-base leading-7 text-slate-600">This payment order expired without confirmation. No subscription or posting credits were activated.</p>
                @else
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">Payment pending</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $package['name'] }} plan</h1>
                    <p class="mt-3 text-base leading-7 text-slate-600">Your subscription is not active until Paystack confirms the payment.</p>
                @endif

                @if (session('payment_error'))
                    <p class="mt-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800" role="alert">{{ session('payment_error') }}</p>
                @endif
                @if (session('payment_pending'))
                    <p class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900" role="status">{{ session('payment_pending') }}</p>
                @endif

                <dl class="mt-7 grid gap-4 rounded-xl bg-slate-50 p-5 sm:grid-cols-2">
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Order reference</dt><dd class="mt-1 break-all font-mono text-sm font-semibold text-slate-950">{{ $order->order_reference }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</dt><dd class="mt-1 font-semibold capitalize text-slate-950">{{ str_replace('_', ' ', $order->status) }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Amount</dt><dd class="mt-1 font-semibold text-slate-950">{{ $order->currency }} {{ number_format($order->amount) }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Payment method</dt><dd class="mt-1 font-semibold text-slate-950">{{ ['mpesa' => 'M-Pesa', 'card' => 'Visa / Mastercard', 'bank_transfer' => 'Bank transfer'][$order->payment_method] ?? $order->payment_method }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Job posting allowance</dt><dd class="mt-1 font-semibold text-slate-950">{{ $order->job_allowance }} jobs</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Plan validity</dt><dd class="mt-1 font-semibold text-slate-950">{{ $package['validity'] }}</dd></div>
                </dl>

                @if ($order->status === 'paid')
                    <a href="{{ route('employer.dashboard') }}" class="brand-btn accent-btn mt-7">Go to employer dashboard</a>
                @elseif ($order->payment_method === 'bank_transfer')
                    <div class="mt-6 rounded-xl border border-brand-200 bg-brand-50 p-5">
                        <h2 class="font-semibold text-slate-950">Bank transfer details</h2>
                        <dl class="mt-3 space-y-2 text-sm text-slate-700">
                            <div class="flex flex-wrap justify-between gap-2"><dt>NCBA account</dt><dd class="font-semibold">1003249278</dd></div>
                            <div class="flex flex-wrap justify-between gap-2"><dt>I&amp;M account</dt><dd class="font-semibold">0020 6092 5461 50</dd></div>
                        </dl>
                        <p class="mt-4 text-sm leading-6 text-slate-600">Transfers require manual verification. No subscription will be activated until payment is verified.</p>
                    </div>
                @elseif ($order->payment_method === 'mpesa' && $order->status === 'pending')
                    <div class="mt-6 rounded-xl border border-brand-200 bg-brand-50 p-5">
                        <h2 class="font-semibold text-slate-950">Authorize the M-Pesa prompt</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-700">{{ $order->paystack_display_text ?: 'Check your phone and authorize the Paystack payment prompt.' }}</p>
                        @if ($order->paystack_phone)
                            <p class="mt-3 text-sm text-slate-600">Prompt sent to <span class="font-semibold">{{ $order->paystack_phone }}</span></p>
                        @endif
                        <p class="mt-3 text-sm leading-6 text-slate-600">This payment normally expires after 180 seconds. The subscription remains inactive until Paystack confirms it.</p>
                    </div>
                @elseif ($order->payment_method === 'card' && $order->paystack_authorization_url && $order->status === 'pending')
                    <a href="{{ $order->paystack_authorization_url }}" class="brand-btn accent-btn mt-6">Continue to secure Paystack checkout</a>
                @endif

                @if ($order->status === 'failed' || ($order->status === 'pending' && !$order->paystack_reference && $order->payment_method !== 'bank_transfer'))
                    <form method="POST" action="{{ route('employer.payment.retry', $order) }}" class="mt-6">
                        @csrf
                        <button type="submit" class="brand-btn accent-btn">Retry payment</button>
                    </form>
                @elseif ($order->status === 'pending' && $order->payment_method !== 'bank_transfer' && $order->paystack_reference)
                    <form method="POST" action="{{ route('employer.payment.check', $order) }}" class="mt-6">
                        @csrf
                        <button type="submit" class="rounded-xl border border-brand-200 px-4 py-3 text-sm font-semibold text-brand-700 hover:bg-brand-50">Check payment status</button>
                    </form>
                @endif

                @if ($order->status !== 'paid')
                    <a href="{{ route('employer.pricing') }}" class="mt-6 block text-sm font-semibold text-brand-700 hover:text-brand-900">Back to plans</a>
                @endif
            </div>
        </div>
    </section>
@endsection