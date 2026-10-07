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
        'currency',
        'paystack_reference',
        'paystack_access_code',
        'paystack_authorization_url',
        'paystack_phone',
        'paystack_display_text',
        'paystack_status',
        'paystack_initiated_at',
        'subscription_id',
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
            'paystack_initiated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(EmployerSubscription::class);
    }
}