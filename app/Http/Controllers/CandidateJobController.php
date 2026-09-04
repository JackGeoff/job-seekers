<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CandidateProfile;
use App\Models\Job;

class CandidateJobController extends Controller
{
    /**
     * Display the candidate dashboard and published jobs.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'candidate') {
            abort(403);
        }

        $query = Job::with('employerProfile')
            ->publiclyVisible()
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        $search = trim(
            $request->input(
                'search',
                $request->input('q', '')
            )
        );

        if ($search !== '') {
            $query->where(function ($q) use ($search) {

                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhereHas('employerProfile', function ($companyQuery) use ($search) {
                        $companyQuery->where(
                            'company_name',
                            'like',
                            "%{$search}%"
                        );
                    });

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Location
        |--------------------------------------------------------------------------
        */

        $location = trim(
            $request->input('location', '')
        );

        if ($location !== '') {
            $query->where(
                'location',
                'like',
                "%{$location}%"
            );
        }

        $jobs = $query
            ->take(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Candidate Applications
        |--------------------------------------------------------------------------
        */

        $candidateProfile = $user->candidateProfile;

        $applications = $candidateProfile
            ? $candidateProfile->applications()
                ->get()
            : collect();

        $applicationCount = $applications->count();

        $underReviewCount = $applications
            ->whereIn('status', [
                'under_review',
                'reviewing',
            ])
            ->count();

        $shortlistedCount = $applications
            ->where('status', 'shortlisted')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Profile Completion
        |--------------------------------------------------------------------------
        */

        $profileCompletion = 0;

        if ($candidateProfile) {
            $profileCompletion = $candidateProfile->completionPercentage();
        }

        return view('dashboard.candidate', [
            'jobs' => $jobs,
            'search' => $search,
            'location' => $location,
            'applicationCount' => $applicationCount,
            'underReviewCount' => $underReviewCount,
            'shortlistedCount' => $shortlistedCount,
            'candidateProfile' => $candidateProfile,
            'profileCompletion' => $profileCompletion,
        ]);
    }
}
