<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateApplicationController extends Controller
{
    /**
     * Show the application form.
     */
    public function create(Request $request, Job $job)
    {
        $user = $request->user();

        if ($user->account_type !== 'candidate') {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Job
        |--------------------------------------------------------------------------
        */

        if ($job->status !== 'published') {
            return redirect()
                ->route('jobs.show', $job)
                ->with('error', 'This job is no longer accepting applications.');
        }

        if (
            $job->application_deadline &&
            $job->application_deadline->isPast()
        ) {
            return redirect()
                ->route('jobs.show', $job)
                ->with('error', 'The application deadline for this job has passed.');
        }

        /*
        |--------------------------------------------------------------------------
        | Candidate Profile
        |--------------------------------------------------------------------------
        */

        $candidateProfile = $user->candidateProfile;
        $hasExistingCv = $candidateProfile?->cv_path
            && Storage::disk('local')->exists($candidateProfile->cv_path);

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Applications
        |--------------------------------------------------------------------------
        */

        if ($candidateProfile) {
            $alreadyApplied = Application::where('job_id', $job->id)
                ->where('candidate_profile_id', $candidateProfile->id)
                ->exists();

            if ($alreadyApplied) {
                return redirect()
                    ->route('jobs.show', $job)
                    ->with('error', 'You have already applied for this job.');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Application Form
        |--------------------------------------------------------------------------
        */

        return view('jobs.apply', [
            'job' => $job,
            'candidateProfile' => $candidateProfile,
            'hasExistingCv' => $hasExistingCv,
        ]);
    }


    /**
     * Submit an application.
     */
    public function store(Request $request, Job $job)
    {
        $user = $request->user();

        if ($user->account_type !== 'candidate') {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Job
        |--------------------------------------------------------------------------
        */

        if ($job->status !== 'published') {
            return redirect()
                ->route('jobs.show', $job)
                ->with('error', 'This job is no longer accepting applications.');
        }

        if (
            $job->application_deadline &&
            $job->application_deadline->isPast()
        ) {
            return redirect()
                ->route('jobs.show', $job)
                ->with('error', 'The application deadline for this job has passed.');
        }

        /*
        |--------------------------------------------------------------------------
        | Get / Create Candidate Profile
        |--------------------------------------------------------------------------
        */

        $candidateProfile = $user->candidateProfile;

        if (!$candidateProfile) {
            $candidateProfile = $user->candidateProfile()->create([
                'full_name' => $user->name,
                'phone' => $user->phone,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Applications
        |--------------------------------------------------------------------------
        */

        $alreadyApplied = Application::where('job_id', $job->id)
            ->where('candidate_profile_id', $candidateProfile->id)
            ->exists();

        if ($alreadyApplied) {
            return redirect()
                ->route('jobs.show', $job)
                ->with('error', 'You have already applied for this job.');
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Application
        |--------------------------------------------------------------------------
        */

        $hasExistingCv = $candidateProfile->cv_path
            && Storage::disk('local')->exists($candidateProfile->cv_path);
        $useExistingCv = $request->boolean('use_existing_cv');

        if ($useExistingCv && !$hasExistingCv) {
            return back()
                ->withErrors(['cv' => 'Your saved CV is no longer available. Please upload a new CV.'])
                ->withInput();
        }

        $validated = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'cv' => [
                $useExistingCv && $hasExistingCv ? 'nullable' : 'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Store CV
        |--------------------------------------------------------------------------
        */

        $previousCvPath = $candidateProfile->cv_path;
        $cvPath = $useExistingCv
            ? $previousCvPath
            : $request->file('cv')->store('cvs', 'local');

        /*
        |--------------------------------------------------------------------------
        | Update Candidate Profile
        |--------------------------------------------------------------------------
        */

        $candidateProfile->update([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'cv_path' => $cvPath,
        ]);

        if (!$useExistingCv && $previousCvPath) {
            Storage::disk('local')->delete($previousCvPath);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Application
        |--------------------------------------------------------------------------
        */

        Application::create([
            'job_id' => $job->id,
            'candidate_profile_id' => $candidateProfile->id,
            'status' => 'submitted',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('jobs.show', $job)
            ->with(
                'success',
                'Application sent successfully.'
            );
    }



    /**
     * Securely download/view a candidate CV.
     *
     * Only the employer who owns the job can access it.
     */
    public function downloadCv(
        Request $request,
        Application $application
    ) {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Employer Only
        |--------------------------------------------------------------------------
        */

        if ($user->account_type !== 'employer') {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Load Relationships
        |--------------------------------------------------------------------------
        */

        $application->load([
            'job',
            'candidateProfile',
        ]);

        $employerProfile = $user->employerProfile;

        if (!$employerProfile) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Make Sure Employer Owns This Job
        |--------------------------------------------------------------------------
        */

        if (
            $application->job->employer_profile_id !==
            $employerProfile->id
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Check CV Exists
        |--------------------------------------------------------------------------
        */

        if (!$application->candidateProfile->cv_path) {
            abort(404, 'CV not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Check File Exists
        |--------------------------------------------------------------------------
        */

        if (!Storage::disk('local')->exists(
            $application->candidateProfile->cv_path
        )) {
            abort(404, 'CV file not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Return CV
        |--------------------------------------------------------------------------
        */

        return Storage::disk('local')->response(
            $application->candidateProfile->cv_path
        );
    }
}
