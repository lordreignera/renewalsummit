<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_path',
        'booking_url',
        'single_price_usd',
        'double_price_usd',
        'single_price_ugx',
        'double_price_ugx',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'single_price_usd' => 'integer',
        'double_price_usd' => 'integer',
        'single_price_ugx' => 'integer',
        'double_price_ugx' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'accommodation_hotel_id');
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(HotelRoomType::class)->orderBy('sort_order');
    }

    public function priceForRoomType(string $currency, string $roomTypeSlug): int
    {
        $roomType = $this->roomTypes()->where('slug', $roomTypeSlug)->first();

        if ($roomType) {
            return $currency === 'USD' ? $roomType->price_usd : $roomType->price_ugx;
        }

        // Legacy fallback for single / double before room-type table existed.
        $isDouble = $roomTypeSlug === 'double';
        if ($currency === 'USD') {
            return $isDouble ? $this->double_price_usd : $this->single_price_usd;
        }

        return $isDouble ? $this->double_price_ugx : $this->single_price_ugx;
    }
}
