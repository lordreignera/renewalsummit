@extends('layouts.admin')
@section('title', 'Upload Video Testimonial')
@section('page-title', 'Upload Video Testimonial')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm p-8">

        <p class="text-sm text-gray-500 mb-6">
            Videos uploaded here are <strong>immediately published</strong> on the landing page.
            Accepted formats: MP4, MOV, WEBM &mdash; up to 100 MB.
        </p>

        @if($errors->any())
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.testimonials.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-bold text-summit mb-1">Person's Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Pastor James Okello"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div>
                <label class="block text-sm font-bold text-summit mb-1">Country <span class="text-red-500">*</span></label>
                <input type="text" name="country" value="{{ old('country') }}" required
                       placeholder="e.g. Uganda, Kenya, USA"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div>
                <label class="block text-sm font-bold text-summit mb-1">Short Message <span class="font-normal text-gray-400">(optional)</span></label>
                <textarea name="message" rows="2" maxlength="500"
                          placeholder="E.g. «Come and experience what God is doing at Renewal Summit!»"
                          class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none">{{ old('message') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-summit mb-1">Video File <span class="text-red-500">*</span></label>
                <input type="file" name="video" required accept="video/mp4,video/quicktime,video/webm"
                       class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <p class="text-xs text-gray-400 mt-1">MP4, MOV, or WEBM &mdash; max 100 MB.</p>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-6 py-3 rounded-xl transition text-sm shadow">
                    Upload &amp; Publish →
                </button>
                <a href="{{ route('admin.testimonials.index') }}"
                   class="px-5 py-3 text-sm font-semibold text-gray-600 hover:text-gray-900 rounded-xl border border-gray-200 hover:border-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
