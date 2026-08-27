<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class EmployerApplicationController extends Controller
{
    /**
     * Display applications submitted to the authenticated employer's jobs.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'employer') {
            abort(403);
        }

        $employerProfile = $user->employerProfile;

        if (!$employerProfile) {
            return redirect()
                ->route('employer.profile')
                ->with('error', 'Please complete your employer profile before reviewing applications.');
        }

        $applications = Application::with([
            'job',
            'candidateProfile.user',
        ])
            ->whereHas('job', function ($query) use ($employerProfile) {
                $query->where('employer_profile_id', $employerProfile->id);
            })
            ->latest()
            ->get();

        return view('employer.applications.index', [
            'applications' => $applications,
        ]);
    }
}
