<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestimonialVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'message',
        'video_path',
        'original_filename',
        'mime_type',
        'size_kb',
        'status',
        'viewed_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];
}
