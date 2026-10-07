@extends('layouts.app')

@section('content')
    <section class="relative overflow-hidden py-10 sm:py-14">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/75 via-white to-accent-50/70"></div>
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('employer.pricing') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">← Back to plans</a>

            <div class="mt-7 max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">Checkout preparation</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Choose a payment method</h1>
                <p class="mt-3 text-base leading-7 text-slate-600">Choose how to pay for {{ $package['name'] }}. Your subscription starts only after the payment is verified.</p>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">{{ $errors->first() }}</div>
            @endif

            <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">Payment method</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                        @foreach ([['mpesa', 'M-Pesa', 'Receive a payment prompt on your phone and approve it with your M-Pesa PIN.'], ['card', 'Visa / Mastercard', 'Pay securely through Paystack Checkout.'], ['bank_transfer', 'Bank transfer', 'Creates an order pending manual verification.']] as [$method, $label, $description])
                            <form method="POST" action="{{ route('employer.payment.order') }}" class="flex">
                                @csrf
                                <input type="hidden" name="package" value="{{ $packageKey }}">
                                <input type="hidden" name="payment_method" value="{{ $method }}">
                                <div class="flex min-h-52 w-full flex-col rounded-2xl border border-slate-200 bg-white p-5 text-left shadow-sm transition hover:border-brand-300 hover:shadow-md">
                                    <span class="text-base font-semibold text-slate-950">{{ $label }}</span>
                                    <span class="mt-2 text-sm leading-6 text-slate-600">{{ $description }}</span>
                                    @if ($method === 'mpesa')
                                        <label for="mpesa_phone" class="mt-4 text-xs font-semibold text-slate-700">M-Pesa phone number</label>
                                        <input id="mpesa_phone" name="mpesa_phone" type="tel" inputmode="tel" autocomplete="tel" required placeholder="0712 345 678" value="{{ old('mpesa_phone', $companyProfile?->phone ?? $account->phone) }}" class="auth-input mt-1 h-10 w-full rounded-lg border border-slate-200 px-3 text-sm">
                                        @error('mpesa_phone')
                                            <span class="mt-1 text-xs text-red-700">{{ $message }}</span>
                                        @enderror
                                    @endif
                                    <button type="submit" class="brand-btn accent-btn mt-auto w-full justify-center">Continue <span class="ml-2" aria-hidden="true">→</span></button>
                                </div>
                            </form>
                        @endforeach
                    </div>
                </div>

                <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-label="Order summary">
                    <h2 class="text-lg font-semibold text-slate-950">Order summary</h2>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Plan</dt><dd class="text-right font-semibold text-slate-950">{{ $package['name'] }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Price</dt><dd class="text-right font-semibold text-slate-950">{{ $package['price'] }}</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Posting allowance</dt><dd class="text-right font-semibold text-slate-950">{{ $package['job_allowance'] }} jobs</dd></div>
                        <div class="flex justify-between gap-4"><dt class="text-slate-500">Plan validity</dt><dd class="text-right font-semibold text-slate-950">{{ $package['validity'] }}</dd></div>
                        @if ($companyProfile?->company_name)
                            <div class="border-t border-slate-100 pt-4"><dt class="text-slate-500">Employer</dt><dd class="mt-1 font-semibold text-slate-950">{{ $companyProfile->company_name }}</dd></div>
                        @endif
                        <div><dt class="text-slate-500">Account</dt><dd class="mt-1 break-all font-semibold text-slate-950">{{ $account->email }}</dd></div>
                    </dl>
                </aside>
            </div>
        </div>
    </section>
@endsection
