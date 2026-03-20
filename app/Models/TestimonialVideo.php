<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'viewed_at'   => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Returns the public URL for the video from whichever disk it is stored on.
     * On production this points to Cloudflare R2; on local dev it falls back gracefully.
     */
    public function getVideoUrlAttribute(): string
    {
        if (empty($this->video_path)) {
            return '';
        }

        return route('testimonials.stream', $this);
    }
}
