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
            ->publiclyVisible()
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
        if (!$job->newQuery()->whereKey($job->getKey())->publiclyVisible()->exists()) {
            abort(404);
        }

        $job->load('employerProfile');

        return view('jobs.show', [
            'job' => $job,
        ]);
    }
}
