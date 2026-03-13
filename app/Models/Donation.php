<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_name',
        'phone',
        'email',
        'amount',
        'currency',
        'payment_method',
        'network',
        'swapp_transaction_id',
        'swapp_response',
        'status',
        'message',
        'paid_at',
    ];

    protected $casts = [
        'amount'         => 'integer',
        'swapp_response' => 'array',
        'paid_at'        => 'datetime',
    ];

    public function getFormattedAmountAttribute(): string
    {
        return 'UGX ' . number_format($this->amount);
    }
}
