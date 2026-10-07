<?php

namespace App\Http\Controllers;

use App\Models\EmployerPaymentOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployerOnboardingController extends Controller
{
    public function pricing(Request $request)
    {
        $this->ensureEmployer($request);

        return view('employer.pricing', [
            'packages' => config('employer_plans', []),
            'enterpriseEnquiry' => $request->boolean('enterprise'),
        ]);
    }

    public function selectPlan(Request $request)
    {
        $this->ensureEmployer($request);

        $packageKey = $request->input('package');
        $packages = config('employer_plans', []);

        if (!is_string($packageKey) || !isset($packages[$packageKey]) || $packageKey === 'enterprise') {
            return back()->withErrors([
                'package' => 'Please choose one of the available paid plans.',
            ]);
        }

        $request->session()->put('employer.selected_package', $packageKey);

        return redirect()->route('employer.payment', $packageKey);
    }

    public function payment(Request $request, ?string $package = null)
    {
        $this->ensureEmployer($request);

        $packageKey = $package ?? session('employer.selected_package');
        $packages = config('employer_plans', []);

        if (!is_string($packageKey) || !isset($packages[$packageKey]) || $packageKey === 'enterprise') {
            return redirect()
                ->route('employer.pricing')
                ->with('error', 'Choose a paid plan before continuing to checkout.');
        }

        return view('employer.payment', [
            'package' => $packages[$packageKey],
            'packageKey' => $packageKey,
            'account' => $request->user(),
            'companyProfile' => $request->user()->employerProfile,
        ]);
    }

    public function paymentPending(Request $request, string $orderReference)
    {
        $this->ensureEmployer($request);

        $order = EmployerPaymentOrder::query()
            ->where('user_id', $request->user()->id)
            ->where('order_reference', $orderReference)
            ->firstOrFail();
        $package = config("employer_plans.{$order->plan}");

        abort_unless(is_array($package), 404);

        if ($order->paystack_reference && $order->status === 'pending') {
            Log::debug('Employer viewed a pending Paystack order.', [
                'order_reference' => $order->order_reference,
                'paystack_reference' => $order->paystack_reference,
            ]);
        }

        return view('employer.payment-pending', compact('order', 'package'));
    }

    public function enterprise(Request $request)
    {
        $this->ensureEmployer($request);

        return redirect()->route('employer.pricing', ['enterprise' => 1]);
    }

    private function ensureEmployer(Request $request): void
    {
        abort_unless($request->user()?->account_type === 'employer', 403);
    }
}