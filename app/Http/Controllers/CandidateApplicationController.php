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
     *
     * Flow:
     *
     * Guest
     * -> Registration
     * -> Candidate Profile
     * -> Application Form
     *
     * Authenticated candidate with incomplete profile
     * -> Candidate Profile
     *
     * Authenticated candidate with complete profile
     * -> Application Form
     */
    public function create(Request $request, Job $job)
    {
        /*
        |--------------------------------------------------------------------------
        | Check Job
        |--------------------------------------------------------------------------
        */

        if ($job->status !== 'published') {
            return redirect()
                ->route('jobs.show', $job)
                ->with(
                    'error',
                    'This job is no longer accepting applications.'
                );
        }

        if (
            $job->application_deadline &&
            $job->application_deadline->isPast()
        ) {
            return redirect()
                ->route('jobs.show', $job)
                ->with(
                    'error',
                    'The application deadline for this job has passed.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Guest User
        |--------------------------------------------------------------------------
        |
        | If someone clicks Apply without an account:
        |
        | Job
        |   ↓
        | Apply
        |   ↓
        | Register
        |
        | We save the job ID in the session so that after registration
        | and profile completion, the candidate returns to this job's
        | application page.
        |
        */

        if (!$request->user()) {

            session([
                'apply_job_id' => $job->id,
            ]);

            return redirect()
                ->route('register')
                ->with(
                    'info',
                    'Create your free account to apply for this job.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Authenticated User
        |--------------------------------------------------------------------------
        */

        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Candidate Only
        |--------------------------------------------------------------------------
        */

        if ($user->account_type !== 'candidate') {
            abort(403);
        }


        /*
        |--------------------------------------------------------------------------
        | Candidate Profile
        |--------------------------------------------------------------------------
        */

        $candidateProfile = $user->candidateProfile;


        /*
        |--------------------------------------------------------------------------
        | Profile Required
        |--------------------------------------------------------------------------
        |
        | The candidate must complete their profile before applying.
        |
        */

        if (!$candidateProfile) {

            session([
                'apply_job_id' => $job->id,
            ]);

            return redirect()
                ->route('candidate.profile')
                ->with(
                    'info',
                    'Please complete your profile and upload your CV before applying.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Required Profile Information
        |--------------------------------------------------------------------------
        |
        | These fields are required for an application.
        |
        */

        $profileIncomplete =
            empty($candidateProfile->full_name) ||
            empty($candidateProfile->phone) ||
            empty($candidateProfile->location) ||
            empty($candidateProfile->job_title);


        if ($profileIncomplete) {

            session([
                'apply_job_id' => $job->id,
            ]);

            return redirect()
                ->route('candidate.profile')
                ->with(
                    'info',
                    'Please complete your profile before applying for this job.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Candidate CV
        |--------------------------------------------------------------------------
        */

        $hasExistingCv =
            !empty($candidateProfile->cv_path) &&
            Storage::disk('local')->exists(
                $candidateProfile->cv_path
            );


        if (!$hasExistingCv) {

            session([
                'apply_job_id' => $job->id,
            ]);

            return redirect()
                ->route('candidate.profile')
                ->with(
                    'info',
                    'Please upload your CV before applying for this job.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Applications
        |--------------------------------------------------------------------------
        */

        $alreadyApplied = Application::where('job_id', $job->id)
            ->where(
                'candidate_profile_id',
                $candidateProfile->id
            )
            ->exists();


        if ($alreadyApplied) {
            return redirect()
                ->route('jobs.show', $job)
                ->with(
                    'error',
                    'You have already applied for this job.'
                );
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
     *
     * The candidate's profile and CV are already completed before
     * reaching this point.
     */
    public function store(Request $request, Job $job)
    {
        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        if (!$user) {
            session([
                'apply_job_id' => $job->id,
            ]);

            return redirect()
                ->route('register')
                ->with(
                    'info',
                    'Create your free account to apply for this job.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Candidate Only
        |--------------------------------------------------------------------------
        */

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
                ->with(
                    'error',
                    'This job is no longer accepting applications.'
                );
        }

        if (
            $job->application_deadline &&
            $job->application_deadline->isPast()
        ) {
            return redirect()
                ->route('jobs.show', $job)
                ->with(
                    'error',
                    'The application deadline for this job has passed.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Candidate Profile
        |--------------------------------------------------------------------------
        */

        $candidateProfile = $user->candidateProfile;


        /*
        |--------------------------------------------------------------------------
        | Profile Must Exist
        |--------------------------------------------------------------------------
        */

        if (!$candidateProfile) {

            session([
                'apply_job_id' => $job->id,
            ]);

            return redirect()
                ->route('candidate.profile')
                ->with(
                    'info',
                    'Please complete your profile before applying.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Required Profile Information
        |--------------------------------------------------------------------------
        */

        $profileIncomplete =
            empty($candidateProfile->full_name) ||
            empty($candidateProfile->phone) ||
            empty($candidateProfile->location) ||
            empty($candidateProfile->job_title);


        if ($profileIncomplete) {

            session([
                'apply_job_id' => $job->id,
            ]);

            return redirect()
                ->route('candidate.profile')
                ->with(
                    'info',
                    'Please complete your profile before applying.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | CV Must Exist
        |--------------------------------------------------------------------------
        */

        $hasExistingCv =
            !empty($candidateProfile->cv_path) &&
            Storage::disk('local')->exists(
                $candidateProfile->cv_path
            );


        if (!$hasExistingCv) {

            session([
                'apply_job_id' => $job->id,
            ]);

            return redirect()
                ->route('candidate.profile')
                ->with(
                    'info',
                    'Please upload your CV before applying.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Applications
        |--------------------------------------------------------------------------
        */

        $alreadyApplied = Application::where('job_id', $job->id)
            ->where(
                'candidate_profile_id',
                $candidateProfile->id
            )
            ->exists();


        if ($alreadyApplied) {
            return redirect()
                ->route('jobs.show', $job)
                ->with(
                    'error',
                    'You have already applied for this job.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Application
        |--------------------------------------------------------------------------
        |
        | The application page only needs the candidate's cover letter.
        | Name, phone, email and CV already exist in the candidate profile.
        |
        */

        $validated = $request->validate([
            'cover_letter' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);


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
        | Clear Saved Apply Job
        |--------------------------------------------------------------------------
        |
        | The candidate has now successfully applied, so we no longer
        | need to remember the job ID.
        |
        */

        session()->forget('apply_job_id');


        /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        |
        | Send the candidate to their dashboard.
        | The dashboard can display the email verification reminder.
        |
        */

        return redirect()
            ->route('candidate.dashboard')
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

        if (!$user || $user->account_type !== 'employer') {
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


        /*
        |--------------------------------------------------------------------------
        | Employer Profile
        |--------------------------------------------------------------------------
        */

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
            !$application->job ||
            $application->job->employer_profile_id !==
            $employerProfile->id
        ) {
            abort(403);
        }


        /*
        |--------------------------------------------------------------------------
        | Candidate Profile
        |--------------------------------------------------------------------------
        */

        if (!$application->candidateProfile) {
            abort(404, 'Candidate profile not found.');
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

        if (
            !Storage::disk('local')->exists(
                $application->candidateProfile->cv_path
            )
        ) {
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