<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'account_type'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function candidateProfile(): HasOne
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function employerProfile(): HasOne
    {
        return $this->hasOne(EmployerProfile::class);
    }

    public function employerSubscriptions(): HasMany
    {
        return $this->hasMany(EmployerSubscription::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            if ($user->account_type === 'employer') {
                $user->employer_onboarding_version = 1;
            }
        });
    }

    public function hasActiveEmployerSubscription(): bool
    {
        return $this->employerSubscriptions()
            ->where('status', 'successful')
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function hasCompletedEmployerProfile(): bool
    {
        $profile = $this->employerProfile;

        return $profile !== null
            && filled($profile->company_name)
            && filled($profile->industry)
            && filled($profile->location)
            && filled($profile->phone);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'employer_onboarding_version' => 'integer',
        ];
    }
}
