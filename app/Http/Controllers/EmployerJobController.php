<?php

namespace App\Http\Controllers;

use App\Models\EmployerSubscription;
use App\Models\Job;
use App\Support\JobCategories;
use App\Support\JobDescriptionSanitizer;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        if (!$user->hasCompletedEmployerProfile()) {
            return redirect()
                ->route('employer.profile')
                ->with(
                    'error',
                    'Please complete your employer profile before posting a job.'
                );
        }

        $activeSubscription = $this->activeSubscription($user);
        $latestSubscription = $this->latestSubscription($user);
        $usedCredits = $activeSubscription
            ? $user->employerSubscriptionCreditsUsed($activeSubscription)
            : ($latestSubscription ? $user->employerSubscriptionCreditsUsed($latestSubscription) : 0);
        $remainingCredits = $activeSubscription
            ? max(0, $activeSubscription->job_allowance - $usedCredits)
            : 0;
        $canPublish = $activeSubscription !== null && $remainingCredits > 0;

        return view('employer.jobs.create', [
            'categoryGroups' => JobCategories::grouped(),
            'activeSubscription' => $activeSubscription,
            'remainingCredits' => $remainingCredits,
            'maxJobForms' => max(1, $remainingCredits),
            'canPublish' => $canPublish,
            'defaultJobStatus' => $canPublish ? 'published' : 'draft',
            'postingMessage' => $this->postingAvailabilityMessage(
                $activeSubscription,
                $latestSubscription,
                $usedCredits
            ),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->account_type !== 'employer') {
            abort(403);
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
        $jobsInput = $request->input('jobs', []);

        if (is_array($jobsInput)) {
            foreach ($jobsInput as $index => $jobInput) {
                if (is_array($jobInput) && is_string($jobInput['description'] ?? null)) {
                    $jobsInput[$index]['description'] = $this->descriptionSanitizer
                        ->sanitize($jobInput['description']);
                }
            }

            $request->merge(['jobs' => $jobsInput]);
        }

        $validated = $request->validate([
            'jobs' => ['required', 'array', 'min:1'],
            'jobs.*.submission_key' => ['required', 'uuid', 'distinct'],
            'jobs.*.title' => ['required', 'string', 'max:255'],
            'jobs.*.description' => ['required', 'string'],
            'jobs.*.category' => ['required', 'string', 'max:255', JobCategories::validationRule()],
            'jobs.*.location' => ['required', 'string', 'max:255'],
            'jobs.*.employment_type' => ['required', 'in:full-time,part-time,contract,temporary,internship'],
            'jobs.*.salary_min' => ['nullable', 'numeric', 'min:0'],
            'jobs.*.salary_max' => ['nullable', 'numeric', 'min:0', 'gte:jobs.*.salary_min'],
            'jobs.*.salary_currency' => ['required', 'string', 'size:3'],
            'jobs.*.application_deadline' => ['nullable', 'date', 'after_or_equal:today'],
            'jobs.*.external_application_url' => ['nullable', 'string', 'max:2048', 'url:http,https'],
            'jobs.*.status' => ['required', 'in:draft,published'],
        ]);

        $jobs = array_values($validated['jobs']);
        foreach ($jobs as $index => $jobData) {
            $this->validateDescriptionText($jobData['description'], "jobs.{$index}.description");
        }

        $submissionKeys = array_column($jobs, 'submission_key');
        $existingKeys = $employerProfile->jobs()
            ->whereIn('submission_key', $submissionKeys)
            ->pluck('submission_key')
            ->all();

        if (count($existingKeys) === count($submissionKeys)) {
            return redirect()->route('employer.jobs.index')->with(
                'success',
                'This job submission was already received.'
            );
        }

        if ($existingKeys !== []) {
            throw ValidationException::withMessages([
                'jobs' => 'Some jobs in this submission were already received. Refresh and review your job list before retrying.',
            ]);
        }

        $publishedCount = collect($jobs)->where('status', 'published')->count();

        try {
            DB::transaction(function () use ($user, $employerProfile, $jobs, $publishedCount): void {
                $subscription = $publishedCount > 0
                    ? $this->activeSubscription($user, true)
                    : null;
                $usedCredits = $subscription
                    ? $user->employerSubscriptionCreditsUsed($subscription)
                    : 0;
                $remainingCredits = $subscription
                    ? max(0, $subscription->job_allowance - $usedCredits)
                    : 0;
                $maxForms = max(1, $remainingCredits);

                if ($publishedCount > 0 && !$subscription) {
                    $latestSubscription = $this->latestSubscription($user);
                    $latestUsed = $latestSubscription
                        ? $user->employerSubscriptionCreditsUsed($latestSubscription)
                        : 0;

                    throw ValidationException::withMessages([
                        'jobs' => $this->postingAvailabilityMessage(null, $latestSubscription, $latestUsed)
                            ?? 'Choose a subscription plan to start posting jobs.',
                    ]);
                }

                if ($publishedCount > $remainingCredits) {
                    throw ValidationException::withMessages([
                        'jobs' => 'Your job posting limit has been reached. Upgrade your plan to publish more jobs.',
                    ]);
                }

                if (count($jobs) > $maxForms) {
                    throw ValidationException::withMessages([
                        'jobs' => 'The number of job forms exceeds your remaining posting credits. Remove extra forms or upgrade your plan.',
                    ]);
                }

                foreach ($jobs as $jobData) {
                    $isPublished = $jobData['status'] === 'published';

                    if ($isPublished && empty($jobData['application_deadline'])) {
                        $jobData['application_deadline'] = today()->addDays(30)->toDateString();
                    }

                    $jobData['subscription_id'] = $isPublished ? $subscription->id : null;
                    $employerProfile->jobs()->create($jobData);
                }
            });
        } catch (QueryException $exception) {
            $existingCount = $employerProfile->jobs()
                ->whereIn('submission_key', $submissionKeys)
                ->count();

            if ($existingCount === count($submissionKeys)) {
                return redirect()->route('employer.jobs.index')->with(
                    'success',
                    'This job submission was already received.'
                );
            }

            throw $exception;
        }

        return redirect()
            ->route('employer.jobs.index')
            ->with(
                'success',
                count($jobs) === 1 ? 'Job created successfully.' : count($jobs) . ' jobs created successfully.'
            );
    }

    public function edit(Request $request, Job $job)
    {
        $this->authorizeEmployerJob($request, $job);

        return view('employer.jobs.edit', [
            'job' => $job,
            'categoryGroups' => JobCategories::grouped(),
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
                JobCategories::validationRule($job->category),
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

        if (
            $validated['status'] === 'draft'
            && ($job->status !== 'draft' || $job->subscription_id !== null)
        ) {
            throw ValidationException::withMessages([
                'status' => 'A previously published job cannot be changed back to a draft.',
            ]);
        }

        if ($validated['status'] === 'closed' && $job->subscription_id === null && $job->status === 'draft') {
            throw ValidationException::withMessages([
                'status' => 'A draft must be published before it can be closed.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Limit When Publishing
        |--------------------------------------------------------------------------
        */

        if ($validated['status'] === 'published' && $job->status !== 'published') {
            DB::transaction(function () use ($request, $job, &$validated): void {
                $subscription = $this->activeSubscription($request->user(), true);
                $latestSubscription = $this->latestSubscription($request->user());
                $usedCredits = $subscription
                    ? $request->user()->employerSubscriptionCreditsUsed($subscription)
                    : ($latestSubscription
                        ? $request->user()->employerSubscriptionCreditsUsed($latestSubscription)
                        : 0);

                if (!$subscription) {
                    throw ValidationException::withMessages([
                        'status' => $this->postingAvailabilityMessage(
                            null,
                            $latestSubscription,
                            $usedCredits
                        ) ?? 'Choose a subscription plan to start posting jobs.',
                    ]);
                }

                if ($job->subscription_id === null && !$this->canPublishAnotherJob($subscription, $request->user())) {
                    throw ValidationException::withMessages([
                        'status' => $this->postingAvailabilityMessage(
                            $subscription,
                            $subscription,
                            $usedCredits
                        ) ?? 'Your job posting limit has been reached. Upgrade your plan to publish more jobs.',
                    ]);
                }

                $validated['subscription_id'] = $job->subscription_id ?? $subscription->id;
                if (empty($validated['application_deadline'])) {
                    $validated['application_deadline'] = today()->addDays(30)->toDateString();
                }

                $job->update($validated);
            });
        } else {
            $job->update($validated);
        }

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

        if ($job->status !== 'published') {
            return back()->withErrors([
                'job' => 'Only published jobs can be closed.',
            ]);
        }

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

        if ($job->status !== 'draft' || $job->subscription_id !== null) {
            return back()
                ->withErrors([
                    'job' => 'Only drafts that have never been published can be deleted.',
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

    private function validateDescriptionText(
        string $description,
        string $field = 'description'
    ): void
    {
        if (!$this->descriptionSanitizer->hasMinimumText($description, 50)) {
            throw ValidationException::withMessages([
                $field => 'The description must contain at least 50 characters of meaningful text.',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get Active Subscription
    |--------------------------------------------------------------------------
    */

    private function activeSubscription(
        $user,
        bool $lockForUpdate = false
    ): ?EmployerSubscription {
        $query = $user->employerSubscriptions()
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
            );

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        return $query->latest('expires_at')->first();
    }

    private function latestSubscription($user): ?EmployerSubscription
    {
        return $user->employerSubscriptions()
            ->where('status', 'successful')
            ->latest('created_at')
            ->first();
    }

    private function canPublishAnotherJob(
        EmployerSubscription $subscription,
        $user
    ): bool
    {
        return $user->employerSubscriptionCreditsUsed($subscription) < $subscription->job_allowance;
    }

    private function postingAvailabilityMessage(
        ?EmployerSubscription $activeSubscription,
        ?EmployerSubscription $latestSubscription,
        int $usedCredits
    ): ?string
    {
        if ($activeSubscription) {
            return $usedCredits >= $activeSubscription->job_allowance
                ? 'Your job posting limit has been reached. Upgrade your plan to publish more jobs.'
                : null;
        }

        if (!$latestSubscription) {
            return 'Choose a subscription plan to start posting jobs.';
        }

        $expired = $latestSubscription->expires_at !== null
            && $latestSubscription->expires_at->isPast();
        $exhausted = $usedCredits >= $latestSubscription->job_allowance;

        if ($expired && $exhausted) {
            return 'Your plan has expired and your posting limit has been reached. Renew or upgrade to continue posting jobs.';
        }

        if ($expired) {
            return 'Your subscription has expired. Renew or upgrade your plan to continue posting jobs.';
        }

        if ($exhausted) {
            return 'Your job posting limit has been reached. Upgrade your plan to publish more jobs.';
        }

        return 'Choose a subscription plan to start posting jobs.';
    }
}