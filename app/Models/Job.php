<?php

namespace App\Models;

use App\Support\JobDescriptionSanitizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    protected $table = 'job_listings';

    protected $fillable = [
        'employer_profile_id',
        'subscription_id',
        'submission_key',
        'title',
        'description',
        'category',
        'location',
        'employment_type',
        'salary_min',
        'salary_max',
        'salary_currency',
        'application_deadline',
        'external_application_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'application_deadline' => 'date',
        ];
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): string => app(JobDescriptionSanitizer::class)
                ->sanitize($value),
            set: fn (?string $value): string => app(JobDescriptionSanitizer::class)
                ->sanitize($value),
        );
    }

    protected function descriptionText(): Attribute
    {
        return Attribute::get(
            fn (): string => app(JobDescriptionSanitizer::class)
                ->plainText($this->description)
        );
    }

    public function employerProfile(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(
            EmployerSubscription::class,
            'subscription_id'
        );
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $query) {
                $query->whereDate('application_deadline', '>=', today())
                    ->orWhere(function (Builder $legacyVisibility) {
                        $legacyVisibility
                            ->whereNull('application_deadline')
                            ->where('created_at', '>=', now()->subDays(30));
                    });
            });
    }
}