<?php

namespace App\Http\Controllers;

use App\Models\EmployerSubscription;
use App\Models\Job;
use App\Support\JobDescriptionSanitizer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmployerJobController extends Controller
{
    public function __construct(
        private readonly JobDescriptionSanitizer $descriptionSanitizer
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'employer') {
            abort(403);
        }

        if (!$this->hasActiveSubscription($user)) {
            return redirect()
                ->route($this->onboardingRoute())
                ->with(
                    'error',
                    'Choose a plan and complete payment before managing jobs.'
                );
        }

        if (!$user->hasCompletedEmployerProfile()) {
            return redirect()
                ->route('employer.profile')
                ->with(
                    'error',
                    'Please complete your employer profile before managing jobs.'
                );
        }

        $employerProfile = $user->employerProfile;

        $jobs = $employerProfile->jobs()
            ->latest()
            ->paginate(10);

        return view('employer.jobs.index', [
            'jobs' => $jobs,
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'employer') {
            abort(403);
        }

        if (!$this->hasActiveSubscription($user)) {
            return redirect()
                ->route($this->onboardingRoute())
                ->with(
                    'error',
                    'Choose a plan and complete payment before posting a job.'
                );
        }

        if (!$user->hasCompletedEmployerProfile()) {
            return redirect()
                ->route('employer.profile')
                ->with(
                    'error',
                    'Please complete your employer profile before posting a job.'
                );
        }

        return view('employer.jobs.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'employer') {
            abort(403);
        }

        $subscription = $this->activeSubscription($user);

        if (!$subscription) {
            return redirect()
                ->route($this->onboardingRoute())
                ->with(
                    'error',
                    'Choose a plan and complete payment before posting a job.'
                );
        }

        if (!$user->hasCompletedEmployerProfile()) {
            return redirect()
                ->route('employer.profile')
                ->with(
                    'error',
                    'Please complete your employer profile before posting a job.'
                );
        }

        $employerProfile = $user->employerProfile;
        $this->sanitizeDescriptionInput($request);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
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

            'external_application_url' => [
                'nullable',
                'string',
                'max:2048',
                'url:http,https',
            ],

            'status' => [
                'required',
                'in:draft,published',
            ],
        ]);

        $this->validateDescriptionText($validated['description']);

        /*
        |--------------------------------------------------------------------------
        | Check Job Posting Limit
        |--------------------------------------------------------------------------
        */

        if ($validated['status'] === 'published') {
            if (!$this->canPublishAnotherJob($subscription)) {
                return back()
                    ->withInput()
                    ->with(
                        'job_limit_reached',
                        true
                    );
            }

            $validated['subscription_id'] = $subscription->id;
        }

        $employerProfile->jobs()->create($validated);

        return redirect()
            ->route('employer.jobs.index')
            ->with(
                'success',
                'Job created successfully.'
            );
    }

    public function edit(Request $request, Job $job)
    {
        $this->authorizeEmployerJob($request, $job);

        return view('employer.jobs.edit', [
            'job' => $job,
        ]);
    }

    public function update(Request $request, Job $job)
    {
        $this->authorizeEmployerJob($request, $job);
        $this->sanitizeDescriptionInput($request);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
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

            'external_application_url' => [
                'nullable',
                'string',
                'max:2048',
                'url:http,https',
            ],

            'status' => [
                'required',
                'in:draft,published,closed',
            ],
        ]);

        $this->validateDescriptionText($validated['description']);

        /*
        |--------------------------------------------------------------------------
        | Check Limit When Publishing
        |--------------------------------------------------------------------------
        */

        if (
            $validated['status'] === 'published'
            && $job->status !== 'published'
        ) {
            $subscription = $this->activeSubscription(
                $request->user()
            );

            if (!$subscription) {
                return redirect()
                    ->route($this->onboardingRoute())
                    ->with(
                        'error',
                        'Your subscription is no longer active. Please choose a plan and complete payment.'
                    );
            }

            if (!$this->canPublishAnotherJob($subscription)) {
                return back()
                    ->withInput()
                    ->with(
                        'job_limit_reached',
                        true
                    );
            }

            $validated['subscription_id'] = $subscription->id;
        }

        $job->update($validated);

        return redirect()
            ->route('employer.jobs.index')
            ->with(
                'success',
                'Job updated successfully.'
            );
    }

    public function close(Request $request, Job $job)
    {
        $this->authorizeEmployerJob($request, $job);

        $job->update([
            'status' => 'closed',
        ]);

        return redirect()
            ->route('employer.jobs.index')
            ->with(
                'success',
                'Job closed successfully.'
            );
    }

    public function destroy(Request $request, Job $job)
    {
        $this->authorizeEmployerJob($request, $job);

        if ($job->status !== 'draft') {
            return back()
                ->withErrors([
                    'job' => 'Only draft jobs can be deleted.',
                ]);
        }

        $job->delete();

        return redirect()
            ->route('employer.jobs.index')
            ->with(
                'success',
                'Draft job deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Authorize Employer Job
    |--------------------------------------------------------------------------
    */

    private function authorizeEmployerJob(
        Request $request,
        Job $job
    ): void {
        $user = $request->user();

        if ($user->account_type !== 'employer') {
            abort(403);
        }

        $employerProfile = $user->employerProfile;

        if (
            !$employerProfile
            || !$user->hasActiveEmployerSubscription()
            || !$user->hasCompletedEmployerProfile()
        ) {
            abort(403);
        }

        if ($job->employer_profile_id !== $employerProfile->id) {
            abort(403);
        }
    }

    private function sanitizeDescriptionInput(Request $request): void
    {
        $description = $request->input('description');

        if (is_string($description)) {
            $request->merge([
                'description' => $this->descriptionSanitizer->sanitize($description),
            ]);
        }
    }

    private function validateDescriptionText(string $description): void
    {
        if (!$this->descriptionSanitizer->hasMinimumText($description, 50)) {
            throw ValidationException::withMessages([
                'description' => 'The description must contain at least 50 characters of meaningful text.',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get Active Subscription
    |--------------------------------------------------------------------------
    */

    private function activeSubscription(
        $user
    ): ?EmployerSubscription {
        return $user->employerSubscriptions()
            ->where('status', 'successful')
            ->where(function ($query) {
                $query
                    ->whereNull('starts_at')
                    ->orWhere(
                        'starts_at',
                        '<=',
                        now()
                    );
            })
            ->where(
                'expires_at',
                '>',
                now()
            )
            ->latest('expires_at')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Check Job Posting Allowance
    |--------------------------------------------------------------------------
    */

    private function canPublishAnotherJob(
        EmployerSubscription $subscription
    ): bool {
        $used = $subscription->jobs()
            ->where('status', 'published')
            ->count();

        return $used < $subscription->job_allowance;
    }

    /*
    |--------------------------------------------------------------------------
    | Check Active Subscription
    |--------------------------------------------------------------------------
    */

    private function hasActiveSubscription(
        $user
    ): bool {
        return $user->hasActiveEmployerSubscription();
    }

    /*
    |--------------------------------------------------------------------------
    | Determine Onboarding Route
    |--------------------------------------------------------------------------
    */

    private function onboardingRoute(): string
    {
        return in_array(
            session('employer.selected_package'),
            [
                'basic',
                'starter',
                'business',
            ],
            true
        )
            ? 'employer.payment'
            : 'employer.pricing';
    }
}