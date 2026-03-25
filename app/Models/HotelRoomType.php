<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelRoomType extends Model
{
    protected $fillable = [
        'hotel_id',
        'slug',
        'label',
        'price_ugx',
        'price_usd',
        'room_count',
        'sort_order',
    ];

    protected $casts = [
        'price_ugx'  => 'integer',
        'price_usd'  => 'integer',
        'room_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
