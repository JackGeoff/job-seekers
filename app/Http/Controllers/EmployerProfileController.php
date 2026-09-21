<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployerProfileController extends Controller
{
    public function create()
    {
        $user = request()->user();

        abort_unless($user->account_type === 'employer', 403);

        if (!$user->hasActiveEmployerSubscription()) {
            return redirect()->route($this->hasSelectedPlan() ? 'employer.payment' : 'employer.pricing')
                ->with('error', 'Choose a plan and complete demo payment before setting up your company profile.');
        }

        $profile = $user->employerProfile;
        $requiredFields = ['company_name', 'industry', 'location', 'phone'];
        $completedFields = collect($requiredFields)->filter(fn ($field) => filled($profile?->{$field}))->count();

        return view('employer.profile', [
            'profile' => $profile,
            'completion' => (int) round(($completedFields / count($requiredFields)) * 100),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'employer') {
            abort(403);
        }

        if (!$user->hasActiveEmployerSubscription()) {
            return redirect()->route($this->hasSelectedPlan() ? 'employer.payment' : 'employer.pricing')
                ->with('error', 'Choose a plan and complete demo payment before setting up your company profile.');
        }

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
        ]);

        $user->employerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('employer.dashboard');
    }

    private function hasSelectedPlan(): bool
    {
        return in_array(session('employer.selected_package'), ['basic', 'starter', 'business'], true);
    }
}
