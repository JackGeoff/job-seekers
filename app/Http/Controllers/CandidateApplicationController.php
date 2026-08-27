<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class CandidateApplicationController extends Controller
{
    /**
     * Apply for a job.
     */
    public function store(Request $request, Job $job)
    {
        $user = $request->user();

        if ($user->account_type !== 'candidate') {
            abort(403);
        }

        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            return redirect()
                ->route('candidate.profile')
                ->with('error', 'Please complete your candidate profile before applying.');
        }

        if ($job->status !== 'published') {
            return back()->with('error', 'This job is no longer accepting applications.');
        }

        if (
            $job->application_deadline &&
            $job->application_deadline->isPast()
        ) {
            return back()->with('error', 'The application deadline for this job has passed.');
        }

        $alreadyApplied = Application::where('job_id', $job->id)
            ->where('candidate_profile_id', $candidateProfile->id)
            ->exists();

        if ($alreadyApplied) {
            return back()->with('error', 'You have already applied for this job.');
        }

        if (!$candidateProfile->cv_path) {
            return redirect()
                ->route('candidate.profile')
                ->with('error', 'Please upload your CV before applying for a job.');
        }

        Application::create([
            'job_id' => $job->id,
            'candidate_profile_id' => $candidateProfile->id,
            'status' => 'submitted',
        ]);

        return back()->with(
            'success',
            'Your application has been submitted successfully.'
        );
    }
}
