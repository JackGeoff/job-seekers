<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployerPaymentOrder extends Model
{
    protected $fillable = [
        'user_id',
        'order_reference',
        'plan',
        'amount',
        'job_allowance',
        'duration_unit',
        'duration_value',
        'payment_method',
        'status',
        'expires_at',
        'paid_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'job_allowance' => 'integer',
            'duration_value' => 'integer',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}