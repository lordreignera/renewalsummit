<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'payment_context',
        'swapp_transaction_id',
        'swapp_reference',
        'payment_method',
        'phone_number',
        'network',
        'amount',
        'currency',
        'status',
        'swapp_response',
        'failure_reason',
        'paid_at',
    ];

    protected $casts = [
        'amount'          => 'integer',
        'swapp_response'  => 'array',
        'paid_at'         => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->currency === 'USD'
            ? '$' . number_format($this->amount) . ' USD'
            : 'UGX ' . number_format($this->amount);
    }
}
