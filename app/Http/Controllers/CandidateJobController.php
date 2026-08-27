<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class CandidateJobController extends Controller
{
    /**
     * Display published jobs to candidates.
     */
    public function index(Request $request)
    {
        $query = Job::with('employerProfile')
            ->where('status', 'published')
            ->latest();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhereHas('employerProfile', function ($companyQuery) use ($search) {
                        $companyQuery->where('company_name', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Location
        |--------------------------------------------------------------------------
        */

        if ($request->filled('location')) {
            $location = trim($request->input('location'));

            $query->where('location', 'like', "%{$location}%");
        }

        $jobs = $query
            ->take(6)
            ->get();

        return view('dashboard.candidate', [
            'jobs' => $jobs,
            'search' => $request->input('search', ''),
            'location' => $request->input('location', ''),
        ]);
    }
}
