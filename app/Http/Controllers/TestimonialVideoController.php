<?php

namespace App\Http\Controllers;

use App\Models\TestimonialVideo;
use Illuminate\Http\StreamedResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialVideoController extends Controller
{
    /**
     * Store a public video testimony submission for admin moderation.
     * Videos are stored on R2 (same cloud disk as QR codes).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => 'required|string|max:191',
            'country' => 'required|string|max:120',
            'message' => 'nullable|string|max:500',
            'video'   => 'required|file|mimetypes:video/mp4,video/quicktime,video/webm|max:10240',
        ], [
            'video.max'       => 'Video must be 10 MB or less.',
            'video.mimetypes' => 'Please upload an MP4, MOV, or WEBM video.',
        ]);

        $disk = config('filesystems.qr_disk', 'r2');
        $file = $request->file('video');
        $path = $file->store('testimonials/videos', $disk);

        TestimonialVideo::create([
            'name'              => $data['name'],
            'country'           => $data['country'],
            'message'           => $data['message'] ?? null,
            'video_path'        => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type'         => $file->getMimeType(),
            'size_kb'           => (int) ceil($file->getSize() / 1024),
            'status'            => 'pending',
        ]);

        return back()->with('testimonial_success', 'Your video testimony was uploaded and is pending approval. Thank you!');
    }

    public function stream(TestimonialVideo $video): StreamedResponse
    {
        abort_unless($video->status === 'approved' || auth()->check(), 404);

        // Try configured disk first (r2), fall back to 'public' for videos
        // uploaded before R2 was enabled.
        $primaryDisk = config('filesystems.qr_disk', 'r2');
        $disk = null;
        foreach ([$primaryDisk, 'public'] as $candidate) {
            try {
                if (! empty($video->video_path) && Storage::disk($candidate)->exists($video->video_path)) {
                    $disk = $candidate;
                    break;
                }
            } catch (\Throwable $e) {
                // disk not configured or unreachable — try next
            }
        }

        if ($disk === null) {
            abort(404, 'Video file not found on any storage disk.');
        }

        $stream = Storage::disk($disk)->readStream($video->video_path);

        if (! is_resource($stream)) {
            abort(404, 'Unable to open video stream.');
        }

        $filename = $video->original_filename ?: basename($video->video_path);
        $mimeType = $video->mime_type ?: 'video/mp4';
        $fileSize = Storage::disk($disk)->size($video->video_path);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => (string) $fileSize,
            'Content-Disposition' => 'inline; filename="' . addslashes($filename) . '"',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
