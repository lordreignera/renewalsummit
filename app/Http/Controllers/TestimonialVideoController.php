<?php

namespace App\Http\Controllers;

use App\Models\TestimonialVideo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TestimonialVideoController extends Controller
{
    /**
     * Store a public video testimony submission for admin moderation.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'country' => 'required|string|max:120',
            'message' => 'nullable|string|max:500',
            'video' => 'required|file|mimetypes:video/mp4,video/quicktime,video/webm|max:3072', // 3MB
        ], [
            'video.max' => 'Video must be 3MB or less.',
            'video.mimetypes' => 'Please upload an MP4, MOV, or WEBM video.',
        ]);

        $file = $request->file('video');
        $path = $file->store('testimonials/videos', 'public');

        TestimonialVideo::create([
            'name' => $data['name'],
            'country' => $data['country'],
            'message' => $data['message'] ?? null,
            'video_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size_kb' => (int) ceil($file->getSize() / 1024),
            'status' => 'pending',
        ]);

        return back()->with('testimonial_success', 'Your video testimony was uploaded and is pending approval. Thank you!');
    }
}
