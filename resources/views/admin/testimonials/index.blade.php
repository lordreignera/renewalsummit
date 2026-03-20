@extends('layouts.admin')
@section('title', 'Video Testimonials')
@section('page-title', 'Video Testimonials')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.testimonials.create') }}"
           class="inline-flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold px-5 py-2.5 rounded-xl shadow transition text-sm">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
            Upload Video
        </a>
    </div>

    <form method="GET" class="bg-white rounded-2xl shadow-sm p-4 flex items-center gap-3 max-w-md">
        <select name="status" class="border border-gray-300 rounded-xl px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach(['pending','approved','rejected'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button class="bg-gold hover:bg-yellow-600 text-white text-sm font-bold px-4 py-2 rounded-xl">Filter</button>
        <a href="{{ route('admin.testimonials.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
    </form>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Country</th>
                        <th class="px-4 py-3 text-left">Message</th>
                        <th class="px-4 py-3 text-left">Video</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($videos as $video)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $video->name }}</td>
                        <td class="px-4 py-3">{{ $video->country }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $video->message ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ $video->video_url }}" target="_blank" class="text-blue-600 hover:underline">Open Video</a>
                            <div class="text-xs text-gray-400">{{ $video->size_kb }} KB</div>
                            <div class="text-xs mt-1 {{ $video->viewed_at ? 'text-green-600' : 'text-amber-600' }}">
                                {{ $video->viewed_at ? 'Viewed' : 'Not viewed yet' }}
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php $c = ['pending' => 'bg-yellow-100 text-yellow-700', 'approved' => 'bg-green-100 text-green-700', 'rejected' => 'bg-red-100 text-red-700']; @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $c[$video->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($video->status) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2 flex-wrap">
                                {{-- Review is always visible --}}
                                <a href="{{ route('admin.testimonials.review', $video) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg">Review</a>

                                @if($video->status === 'approved')
                                    {{-- Approved: Approve & Reject hidden, only Review + Delete remain --}}
                                @elseif($video->viewed_at)
                                    <form method="POST" action="{{ route('admin.testimonials.approve', $video) }}">
                                        @csrf
                                        <button class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.testimonials.reject', $video) }}">
                                        @csrf
                                        <button class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg">Reject</button>
                                    </form>
                                @else
                                    <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-1 rounded-lg">View first</span>
                                @endif

                                {{-- Delete is always visible and high-contrast --}}
                                <form method="POST" action="{{ route('admin.testimonials.destroy', $video) }}" onsubmit="return confirm('Delete this testimonial permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button style="background:#7f1d1d;color:#fff;font-size:.75rem;font-weight:700;padding:.375rem .75rem;border-radius:.5rem;border:none;cursor:pointer;" onmouseover="this.style.background='#450a0a'" onmouseout="this.style.background='#7f1d1d'">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No videos found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($videos->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $videos->links() }}</div>
        @endif
    </div>
</div>
@endsection
