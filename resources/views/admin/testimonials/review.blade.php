@extends('layouts.admin')
@section('title', 'Review Testimonial Video')
@section('page-title', 'Review Testimonial Video')

@section('content')
<div class="max-w-4xl space-y-4">
    <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $video->name }}</h2>
            <p class="text-sm text-gray-500">{{ $video->country }}</p>
        </div>

        @if($video->message)
            <p class="text-sm text-gray-700">{{ $video->message }}</p>
        @endif

        <div id="video-wrap">
            <video controls class="w-full rounded-xl bg-black" preload="metadata"
                   onerror="document.getElementById('video-wrap').innerHTML='<div class=\'mt-2 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm\'>⚠️ Video file could not be loaded. It may have been stored before cloud storage was enabled and has since been removed from the server ephemeral disk. Ask the user to re-upload their testimonial.<\/div>'">
                <source src="{{ $video->video_url }}" type="{{ $video->mime_type ?: 'video/mp4' }}"
                        onerror="this.parentElement.dispatchEvent(new Event('error'))">
                Your browser does not support the video tag.
            </video>
        </div>

        <div class="text-xs text-gray-500">
            File: {{ $video->original_filename ?: 'Uploaded video' }} | {{ $video->size_kb }} KB
        </div>

        <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-100">
            <form method="POST" action="{{ route('admin.testimonials.viewed', $video) }}">
                @csrf
                <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-lg">
                    {{ $video->viewed_at ? 'Viewed' : 'Mark as Viewed' }}
                </button>
            </form>

            <form method="POST" action="{{ route('admin.testimonials.approve', $video) }}">
                @csrf
                <button class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold px-4 py-2 rounded-lg" {{ $video->viewed_at ? '' : 'disabled' }}>
                    Approve
                </button>
            </form>

            <form method="POST" action="{{ route('admin.testimonials.reject', $video) }}">
                @csrf
                <button class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-4 py-2 rounded-lg" {{ $video->viewed_at ? '' : 'disabled' }}>
                    Reject
                </button>
            </form>

            <form method="POST" action="{{ route('admin.testimonials.destroy', $video) }}" onsubmit="return confirm('Delete this testimonial permanently?');">
                @csrf
                @method('DELETE')
                <button style="background:#7f1d1d;color:#fff;font-size:.875rem;font-weight:700;padding:.5rem 1rem;border-radius:.5rem;border:none;cursor:pointer;" onmouseover="this.style.background='#450a0a'" onmouseout="this.style.background='#7f1d1d'">Delete</button>
            </form>

            <a href="{{ route('admin.testimonials.index') }}" class="px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900">Back</a>
        </div>

        @if(!$video->viewed_at)
            <p class="text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-3">
                You must click "Mark as Viewed" before approving or rejecting this testimonial.
            </p>
        @endif
    </div>
</div>
@endsection
