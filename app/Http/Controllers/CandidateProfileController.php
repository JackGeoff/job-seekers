<?php

namespace App\Http\Controllers;

use App\Models\CandidateProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateProfileController extends Controller
{
    /**
     * Show the candidate profile page.
     */
    public function create()
    {
        $user = request()->user();

        if ($user->account_type !== 'candidate') {
            abort(403);
        }

        $profile = $user->candidateProfile;

        $fields = CandidateProfile::REQUIRED_COMPLETION_FIELDS;

        $completion = $profile?->completionPercentage() ?? 0;

        return view('candidate.profile', [
            'profile' => $profile,
            'completion' => $completion,
            'requiredFields' => $fields,
        ]);
    }


    /**
     * Save/update candidate profile.
     *
     * This also handles:
     *
     * - First CV upload
     * - Replacing an existing CV
     * - Continuing an application after profile completion
     */
    public function store(Request $request)
    {
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
        | Validate Profile
        |--------------------------------------------------------------------------
        */

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

            'location' => [
                'required',
                'string',
                'max:255',
            ],

            'job_title' => [
                'required',
                'string',
                'max:255',
            ],

            'skills' => [
                'nullable',
                'string',
            ],

            'education' => [
                'nullable',
                'string',
            ],

            'experience' => [
                'nullable',
                'string',
            ],

            'bio' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | CV
            |--------------------------------------------------------------------------
            |
            | Nullable because an existing CV does not need to be uploaded
            | again when the candidate is simply editing their profile.
            |
            */

            'cv' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Get Existing Profile
        |--------------------------------------------------------------------------
        */

        $profile = $user->candidateProfile;


        /*
        |--------------------------------------------------------------------------
        | Remember Previous CV
        |--------------------------------------------------------------------------
        */

        $previousCvPath = $profile?->cv_path;


        /*
        |--------------------------------------------------------------------------
        | Update Profile Information
        |--------------------------------------------------------------------------
        */

        $profileData = collect($validated)
            ->except('cv')
            ->toArray();


        $profile = $user->candidateProfile()->updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            $profileData
        );


        /*
        |--------------------------------------------------------------------------
        | Upload / Replace CV
        |--------------------------------------------------------------------------
        |
        | If the candidate selected a new CV:
        |
        | 1. Store the new CV.
        | 2. Update the profile.
        | 3. Delete the previous CV.
        |
        */

        if ($request->hasFile('cv')) {

            $cvPath = $request
                ->file('cv')
                ->store('cvs', 'local');


            $profile->update([
                'cv_path' => $cvPath,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Delete Previous CV
            |--------------------------------------------------------------------------
            |
            | Only delete the old file after the new file has been
            | successfully stored.
            |
            */

            if (
                $previousCvPath &&
                $previousCvPath !== $cvPath &&
                Storage::disk('local')->exists($previousCvPath)
            ) {
                Storage::disk('local')->delete($previousCvPath);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Continue Job Application
        |--------------------------------------------------------------------------
        |
        | If the candidate originally clicked:
        |
        | Job -> Apply
        |
        | the job ID was saved in the session.
        |
        | After completing the profile/CV, send them back to:
        |
        | Job -> Application Form
        |
        */

        $applyJobId = session('apply_job_id');


        if ($applyJobId) {

            /*
            |--------------------------------------------------------------------------
            | Make Sure The Job Still Exists
            |--------------------------------------------------------------------------
            */

            $jobExists = \App\Models\Job::where('id', $applyJobId)
                ->exists();


            if ($jobExists) {

                /*
                |--------------------------------------------------------------------------
                | Remove Session Value
                |--------------------------------------------------------------------------
                |
                | The application page will handle the rest of the flow.
                |
                */

                session()->forget('apply_job_id');


                return redirect()
                    ->route(
                        'candidate.jobs.apply.create',
                        ['job' => $applyJobId]
                    )
                    ->with(
                        'success',
                        'Your profile has been saved. You can now complete your application.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Job No Longer Exists
            |--------------------------------------------------------------------------
            */

            session()->forget('apply_job_id');
        }


        /*
        |--------------------------------------------------------------------------
        | Normal Profile Update
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('candidate.dashboard')
            ->with(
                'success',
                'Your profile has been updated successfully.'
            );
    }


    /**
     * View the candidate's saved CV.
     */
    public function viewCv(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'candidate') {
            abort(403);
        }


        $profile = $user->candidateProfile;


        /*
        |--------------------------------------------------------------------------
        | Check CV Exists
        |--------------------------------------------------------------------------
        */

        if (!$profile?->cv_path) {
            abort(404, 'CV not found.');
        }


        /*
        |--------------------------------------------------------------------------
        | Check File Exists
        |--------------------------------------------------------------------------
        */

        if (
            !Storage::disk('local')->exists(
                $profile->cv_path
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
            $profile->cv_path
        );
    }
}