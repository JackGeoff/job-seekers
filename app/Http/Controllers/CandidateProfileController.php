<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CandidateProfileController extends Controller
{
    public function create()
    {
        return view('candidate.profile', [
            'profile' => request()->user()->candidateProfile,
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
        ]);

        $user->candidateProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        return redirect()->route('candidate.dashboard');
    }
}
