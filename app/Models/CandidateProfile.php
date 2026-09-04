<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateProfile extends Model
{
    public const REQUIRED_COMPLETION_FIELDS = [
        'full_name',
        'phone',
        'location',
        'job_title',
        'cv_path',
    ];

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'location',
        'job_title',
        'skills',
        'education',
        'experience',
        'cv_path',
        'bio',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function completionPercentage(): int
    {
        $completedFields = collect(self::REQUIRED_COMPLETION_FIELDS)
            ->filter(fn ($field) => filled($this->{$field}))
            ->count();

        return (int) round(
            ($completedFields / count(self::REQUIRED_COMPLETION_FIELDS)) * 100
        );
    }
}
