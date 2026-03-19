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
        // Delete uploaded file from public storage when an admin removes a testimonial.
        if (!empty($video->video_path) && Storage::disk('public')->exists($video->video_path)) {
            Storage::disk('public')->delete($video->video_path);
        }

        $video->delete();

        return back()->with('success', 'Video deleted successfully.');
    }
}
