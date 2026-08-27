<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Display published jobs.
     */
    public function index(Request $request)
    {
        $query = Job::with('employerProfile')
            ->where('status', 'published')
            ->latest();

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));

            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('category', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('employerProfile', function ($companyQuery) use ($keyword) {
                        $companyQuery->where('company_name', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('location')) {
            $location = trim($request->input('location'));

            $query->where(
                'location',
                'like',
                "%{$location}%"
            );
        }

        $jobs = $query->paginate(10)->withQueryString();

        return view('jobs.index', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * Display a single published job.
     */
    public function show(Job $job)
    {
        if ($job->status !== 'published') {
            abort(404);
        }

        return view('jobs.show', [
            'job' => $job,
        ]);
    }
}
