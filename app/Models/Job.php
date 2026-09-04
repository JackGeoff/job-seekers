<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Job extends Model
{
    protected $table = 'job_listings';

    protected $fillable = [
        'employer_profile_id',
        'title',
        'description',
        'category',
        'location',
        'employment_type',
        'salary_min',
        'salary_max',
        'salary_currency',
        'application_deadline',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'application_deadline' => 'date',
        ];
    }

    public function employerProfile(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class);
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
                $query->whereNull('application_deadline')
                    ->orWhereDate('application_deadline', '>=', today());
            });
    }
}
