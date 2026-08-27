<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class EmployerJobController extends Controller
{
    /**
     * Display the employer's jobs.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'employer') {
            abort(403);
        }

        $employerProfile = $user->employerProfile;

        if (!$employerProfile) {
            return redirect()
                ->route('employer.profile')
                ->with('error', 'Please complete your employer profile before managing jobs.');
        }

        $jobs = $employerProfile->jobs()
            ->latest()
            ->paginate(10);

        return view('employer.jobs.index', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * Show the create job form.
     */
    public function create(Request $request)
    {
        if ($request->user()->account_type !== 'employer') {
            abort(403);
        }

        if (!$request->user()->employerProfile) {
            return redirect()
                ->route('employer.profile')
                ->with('error', 'Please complete your employer profile before posting a job.');
        }

        return view('employer.jobs.create');
    }

    /**
     * Store a new job.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'employer') {
            abort(403);
        }

        $employerProfile = $user->employerProfile;

        if (!$employerProfile) {
            return redirect()
                ->route('employer.profile')
                ->with('error', 'Please complete your employer profile before posting a job.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],

            'description' => [
                'required',
                'string',
                'min:50',
            ],

            'category' => [
                'required',
                'string',
                'max:255',
            ],

            'location' => [
                'required',
                'string',
                'max:255',
            ],

            'employment_type' => [
                'required',
                'in:full-time,part-time,contract,temporary,internship',
            ],

            'salary_min' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'salary_max' => [
                'nullable',
                'numeric',
                'min:0',
                'gte:salary_min',
            ],

            'salary_currency' => [
                'required',
                'string',
                'size:3',
            ],

            'application_deadline' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'status' => [
                'required',
                'in:draft,published',
            ],
        ]);

        $employerProfile->jobs()->create($validated);

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', 'Job created successfully.');
    }

    /**
     * Show the edit form.
     */
    public function edit(Request $request, Job $job)
    {
        $this->authorizeEmployerJob($request, $job);

        return view('employer.jobs.edit', [
            'job' => $job,
        ]);
    }

    /**
     * Update an existing job.
     */
    public function update(Request $request, Job $job)
    {
        $this->authorizeEmployerJob($request, $job);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],

            'description' => [
                'required',
                'string',
                'min:50',
            ],

            'category' => [
                'required',
                'string',
                'max:255',
            ],

            'location' => [
                'required',
                'string',
                'max:255',
            ],

            'employment_type' => [
                'required',
                'in:full-time,part-time,contract,temporary,internship',
            ],

            'salary_min' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'salary_max' => [
                'nullable',
                'numeric',
                'min:0',
                'gte:salary_min',
            ],

            'salary_currency' => [
                'required',
                'string',
                'size:3',
            ],

            'application_deadline' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'status' => [
                'required',
                'in:draft,published,closed',
            ],
        ]);

        $job->update($validated);

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', 'Job updated successfully.');
    }

    /**
     * Close a job.
     */
    public function close(Request $request, Job $job)
    {
        $this->authorizeEmployerJob($request, $job);

        $job->update([
            'status' => 'closed',
        ]);

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', 'Job closed successfully.');
    }

    /**
     * Delete a draft job.
     */
    public function destroy(Request $request, Job $job)
    {
        $this->authorizeEmployerJob($request, $job);

        if ($job->status !== 'draft') {
            return back()->withErrors([
                'job' => 'Only draft jobs can be deleted.',
            ]);
        }

        $job->delete();

        return redirect()
            ->route('employer.jobs.index')
            ->with('success', 'Draft job deleted successfully.');
    }

    /**
     * Ensure the authenticated employer owns the job.
     */
    private function authorizeEmployerJob(Request $request, Job $job): void
    {
        $user = $request->user();

        if ($user->account_type !== 'employer') {
            abort(403);
        }

        $employerProfile = $user->employerProfile;

        if (!$employerProfile) {
            abort(403);
        }

        if ($job->employer_profile_id !== $employerProfile->id) {
            abort(403);
        }
    }
}
