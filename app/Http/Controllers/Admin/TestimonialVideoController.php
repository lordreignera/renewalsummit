<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestimonialVideo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TestimonialVideoController extends Controller
{
    public function create(): View
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'    => 'required|string|max:191',
            'country' => 'required|string|max:120',
            'message' => 'nullable|string|max:500',
            'video'   => 'required|file|mimetypes:video/mp4,video/quicktime,video/webm|max:204800',
        ], [
            'video.max'       => 'Video must be 200 MB or less.',
            'video.mimetypes' => 'Please upload an MP4, MOV, or WEBM video.',
        ]);

        $file = $request->file('video');
        $path = $file->store('testimonials/videos', 'r2');

        TestimonialVideo::create([
            'name'              => $request->name,
            'country'           => $request->country,
            'message'           => $request->message ?? null,
            'video_path'        => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type'         => $file->getMimeType(),
            'size_kb'           => (int) ceil($file->getSize() / 1024),
            'status'            => 'approved',
            'viewed_at'         => now(),
            'approved_at'       => now(),
        ]);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Video uploaded and published to the landing page.');
    }

    public function index(Request $request): View
    {
        $query = TestimonialVideo::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $videos = $query->paginate(20)->withQueryString();

        return view('admin.testimonials.index', compact('videos'));
    }

    public function approve(TestimonialVideo $video): RedirectResponse
    {
        if (is_null($video->viewed_at)) {
            return redirect()
                ->route('admin.testimonials.review', $video)
                ->with('warning', 'Please view this video first before approving it.');
        }

        $video->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejected_at' => null,
        ]);

        return back()->with('success', 'Video approved and now visible on the landing page.');
    }

    public function reject(TestimonialVideo $video): RedirectResponse
    {
        if (is_null($video->viewed_at)) {
            return redirect()
                ->route('admin.testimonials.review', $video)
                ->with('warning', 'Please view this video first before rejecting it.');
        }

        $video->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'approved_at' => null,
        ]);

        return back()->with('warning', 'Video rejected.');
    }

    public function review(TestimonialVideo $video): View
    {
        return view('admin.testimonials.review', compact('video'));
    }

    public function markViewed(TestimonialVideo $video): RedirectResponse
    {
        if (is_null($video->viewed_at)) {
            $video->update([
                'viewed_at' => now(),
            ]);
        }

        return back()->with('success', 'Video marked as viewed. You can now approve or reject it.');
    }

    public function destroy(TestimonialVideo $video): RedirectResponse
    {
        // Delete uploaded file from R2 cloud storage.
        $disk = config('filesystems.qr_disk', 'r2');
        if (!empty($video->video_path) && Storage::disk($disk)->exists($video->video_path)) {
            Storage::disk($disk)->delete($video->video_path);
        }

        $video->delete();

        return back()->with('success', 'Video deleted successfully.');
    }
}
