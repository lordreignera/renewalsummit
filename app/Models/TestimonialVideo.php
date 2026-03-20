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
    /**
     * Returns a URL to play the video.
     * - For R2: generates a 4-hour presigned URL so the browser fetches directly
     *   from R2 (fast, supports seeking, survives horizontal scaling).
     * - Fallback: stream route (for legacy local-disk videos).
     */
    public function getVideoUrlAttribute(): string
    {
        if (empty($this->video_path)) {
            return '';
        }

        try {
            if (Storage::disk('r2')->exists($this->video_path)) {
                return Storage::disk('r2')->temporaryUrl(
                    $this->video_path,
                    now()->addHours(4)
                );
            }
        } catch (\Throwable $e) {
            // R2 not reachable (e.g. local dev without credentials) — fall through
        }

        // Legacy fallback: stream through app (works on local public disk)
        return route('testimonials.stream', $this);
    }
}
