<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateProfileController extends Controller
{
    public function create()
    {
        $profile = request()->user()->candidateProfile;

        $fields = [
            'full_name',
            'phone',
            'location',
            'job_title',
            'skills',
            'education',
            'experience',
            'bio',
        ];

        $completedFields = collect($fields)
            ->filter(fn ($field) => filled($profile?->{$field}))
            ->count();

        $completion = (int) round(
            ($completedFields / count($fields)) * 100
        );

        return view('candidate.profile', [
            'profile' => $profile,
            'completion' => $completion,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'candidate') {
            abort(403);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'location' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'skills' => 'nullable|string',
            'education' => 'nullable|string',
            'experience' => 'nullable|string',
            'bio' => 'nullable|string',

            'cv' => [
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120',
            ],
        ]);

        $profile = $user->candidateProfile;

        $profileData = collect($validated)
            ->except('cv')
            ->toArray();

        $profile = $user->candidateProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        if ($request->hasFile('cv')) {

            if ($profile->cv_path) {
                Storage::disk('local')->delete($profile->cv_path);
            }

            $cvPath = $request->file('cv')->store('cvs', 'local');

            $profile->update([
                'cv_path' => $cvPath,
            ]);
        }

        return redirect()
            ->route('candidate.dashboard')
            ->with('success', 'Your profile has been updated successfully.');
    }
}
