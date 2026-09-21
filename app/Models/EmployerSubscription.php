<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployerSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan',
        'amount',
        'payment_method',
        'status',
        'transaction_reference',
        'paid_at',
        'starts_at',
        'expires_at',
        'job_allowance',
    ];

    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'amount' => 'integer',
            'job_allowance' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
