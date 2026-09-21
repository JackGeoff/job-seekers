@extends('layouts.app')

@section('content')
    <section
        class="relative overflow-hidden py-12 sm:py-16 lg:py-20"
        x-data="{
            method: 'mpesa',
            processing: false,
            validationError: '',
            submitPayment(form) {
                if (this.processing) return;

                this.validationError = '';

                if (!form.checkValidity()) {
                    this.validationError = 'Please complete the highlighted fields with valid demo details.';
                    form.reportValidity();

                    return;
                }

                this.processing = true;
            }
        }"
    >
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-96 bg-gradient-to-br from-brand-100/80 via-white to-accent-50/80"></div>
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mx-auto mb-6 max-w-3xl rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                    Please enter valid demo payment details and try again.
                </div>
            @endif
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.16em] text-accent-600">Demo payment step</p>
                <h1 class="mt-3 text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">Pay for {{ $package['name'] }}</h1>
                <p class="mt-4 text-base leading-7 text-slate-600">Choose M-Pesa or Card to complete your demo payment.</p>
            </div>

            <div class="mx-auto mt-10 max-w-3xl rounded-3xl border border-brand-100 bg-white p-6 shadow-xl shadow-brand-900/10 sm:p-8">
                <div class="grid grid-cols-2 gap-2 rounded-2xl bg-brand-50 p-1" role="tablist" aria-label="Payment method">
                    <button type="button" role="tab" :aria-selected="method === 'mpesa'" @click="method = 'mpesa'; validationError = ''" :class="method === 'mpesa' ? 'bg-white text-brand-800 shadow-sm' : 'text-slate-500 hover:text-brand-800'" class="rounded-xl px-4 py-3 text-sm font-bold transition">Pay with M-Pesa</button>
                    <button type="button" role="tab" :aria-selected="method === 'card'" @click="method = 'card'; validationError = ''" :class="method === 'card' ? 'bg-white text-brand-800 shadow-sm' : 'text-slate-500 hover:text-brand-800'" class="rounded-xl px-4 py-3 text-sm font-bold transition">Pay with Card</button>
                </div>
                <p x-show="validationError" x-text="validationError" class="mt-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm font-medium text-red-800" role="alert"></p>

                <form method="POST" action="{{ route('employer.payment.complete') }}" x-show="method === 'mpesa'" x-cloak novalidate class="mt-8 space-y-5" @submit="submitPayment($event.target)">
                    @csrf
                    <input type="hidden" name="payment_method" value="mpesa">
                    <h2 class="text-xl font-semibold text-slate-950">Pay with M-Pesa for {{ $package['name'] }}</h2>
                    <div>
                        <label for="mpesa_phone" class="mb-2 block text-sm font-semibold text-slate-800">M-Pesa phone number</label>
                        <input id="mpesa_phone" name="mpesa_phone" type="tel" inputmode="tel" required pattern="(?:254|0)[17][0-9]{8}" placeholder="e.g. 0712 345 678" class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition">
                        <p class="mt-2 text-xs text-slate-500">Use a Kenyan number beginning with 07, 01 or 254.</p>
                    </div>
                    <button type="submit" :disabled="processing" class="brand-btn accent-btn w-full disabled:cursor-wait disabled:opacity-60"><span x-text="processing ? 'Processing demo payment...' : 'Continue with M-Pesa'"></span></button>
                </form>

                <form method="POST" action="{{ route('employer.payment.complete') }}" x-show="method === 'card'" x-cloak novalidate class="mt-8 space-y-5" @submit="submitPayment($event.target)">
                    @csrf
                    <input type="hidden" name="payment_method" value="card">
                    <h2 class="text-xl font-semibold text-slate-950">Pay with Card for {{ $package['name'] }}</h2>
                    <div>
                        <label for="cardholder_name" class="mb-2 block text-sm font-semibold text-slate-800">Cardholder name</label>
                        <input id="cardholder_name" name="cardholder_name" type="text" required autocomplete="off" placeholder="Jane Wanjiku" class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition">
                    </div>
                    <div>
                        <label for="card_number" class="mb-2 block text-sm font-semibold text-slate-800">Card number</label>
                        <input id="card_number" name="card_number" type="text" required inputmode="numeric" autocomplete="off" pattern="[0-9 ]{12,23}" placeholder="1111 2222 3333 4444" class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition">
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div><label for="card_expiry" class="mb-2 block text-sm font-semibold text-slate-800">Expiry date</label><input id="card_expiry" name="card_expiry" type="text" required autocomplete="off" pattern="(0[1-9]|1[0-2])\/[0-9]{2}" placeholder="MM/YY" class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"></div>
                        <div><label for="card_cvv" class="mb-2 block text-sm font-semibold text-slate-800">CVV</label><input id="card_cvv" name="card_cvv" type="password" required inputmode="numeric" autocomplete="off" pattern="[0-9]{3,4}" placeholder="123" class="auth-input h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-slate-950 outline-none transition"></div>
                    </div>
                    <button type="submit" :disabled="processing" class="brand-btn accent-btn w-full disabled:cursor-wait disabled:opacity-60"><span x-text="processing ? 'Processing demo payment...' : 'Continue with Card'"></span></button>
                </form>
            </div>

            <div x-show="false" x-cloak class="mx-auto mt-6 max-w-3xl rounded-3xl border border-emerald-200 bg-white p-6 shadow-xl shadow-brand-900/10 sm:p-10">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-100 text-2xl text-emerald-700" aria-hidden="true">✓</div>
                <p class="mt-6 text-sm font-bold uppercase tracking-[0.14em] text-emerald-700">Demo confirmation</p>
                <h2 class="mt-2 text-3xl font-semibold text-slate-950">Payment Successful</h2>
                <p class="mt-3 text-base leading-7 text-slate-600">Your payment has been received successfully.</p>
                <dl class="mt-7 grid gap-4 rounded-2xl bg-brand-50 p-5 sm:grid-cols-2">
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-brand-700">Package</dt><dd class="mt-1 font-semibold text-slate-950">{{ $package['name'] }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-brand-700">Amount</dt><dd class="mt-1 font-semibold text-slate-950">{{ $package['price'] }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-brand-700">Payment method</dt><dd class="mt-1 font-semibold text-slate-950" x-text="method === 'mpesa' ? 'M-Pesa' : 'Card'"></dd></div>
                    <div><dt class="text-xs font-bold uppercase tracking-wide text-brand-700">Demo reference</dt><dd class="mt-1 font-semibold text-slate-950" x-text="transactionReference"></dd></div>
                </dl>
                <p class="mt-5 text-xs leading-5 text-slate-500">This is a simulated confirmation for onboarding testing. No transaction was processed.</p>
                <a href="{{ route('employer.profile') }}" class="brand-btn accent-btn mt-7 w-full sm:w-auto">Complete Company Profile <span class="ml-2" aria-hidden="true">→</span></a>
            </div>
        </div>
    </section>
@endsection
