<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CandidateApplicationsController extends Controller
{
    /**
     * Display applications belonging to the authenticated candidate.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'candidate') {
            abort(403);
        }

        $candidateProfile = $user->candidateProfile;

        $applications = $candidateProfile
            ? $candidateProfile->applications()
                ->with([
                    'job.employerProfile',
                ])
                ->latest()
                ->get()
            : collect();

        return view('candidate.applications.index', [
            'applications' => $applications,
        ]);
    }
}
