<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'qr_token',
        'full_name',
        'designation',
        'designation_specify',
        'phone',
        'email',
        'address',
        'country_type',
        'nationality',
        'affiliation',
        'fcc_region',
        'fcc_regional_leader',
        'fcc_church',
        'fcc_pastor',
        'currency',
        'base_fee',
        'total_amount',
        'current_step',
        'status',
        'qr_code_path',
        'qr_sent_at',
        'checked_in_at',
        // Manual registration fields
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_conditions',
        'allergies',
        'mobility_needs',
        'special_needs',
        'accommodation_required',
        'accommodation_choice',
        'accommodation_hotel_id',
        'accommodation_booking_mode',
        'accommodation_room_type',
        'accommodation_nights',
        'accommodation_currency',
        'accommodation_fee',
        'accommodation_payment_status',
        'sms_opt_in',
        'admin_notes',
    ];

    protected $casts = [
        'base_fee'       => 'integer',
        'total_amount'   => 'integer',
        'current_step'   => 'integer',
        'accommodation_hotel_id' => 'integer',
        'accommodation_nights' => 'integer',
        'qr_sent_at'     => 'datetime',
        'checked_in_at'  => 'datetime',
        'accommodation_required' => 'boolean',
        'sms_opt_in'             => 'boolean',
    ];

    /**
     * Generate a unique human-readable reference on creation.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Registration $reg) {
            $reg->reference = $reg->reference ?? static::generateReference();
            $reg->qr_token  = $reg->qr_token  ?? Str::uuid()->toString();
        });
    }

    public static function generateReference(): string
    {
        $last = static::max('id') ?? 0;
        return 'RS2026-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }

    /* ─── Relationships ─────────────────────────────────────────── */

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function accommodationHotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'accommodation_hotel_id');
    }

    /* ─── Helpers ───────────────────────────────────────────────── */

    public function isFcc(): bool
    {
        return $this->affiliation === 'fcc';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isCheckedIn(): bool
    {
        return $this->status === 'checked_in';
    }

    public function getFormattedTotalAttribute(): string
    {
        if ($this->currency === 'USD') {
            return '$' . number_format($this->total_amount);
        }
        return 'UGX ' . number_format($this->total_amount);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'      => 'secondary',
            'pending'    => 'warning',
            'paid'       => 'success',
            'checked_in' => 'info',
            'cancelled'  => 'danger',
            default      => 'secondary',
        };
    }
}
