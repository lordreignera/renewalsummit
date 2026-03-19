@extends('layouts.admin')
@section('title', 'Accommodation Hotels')
@section('page-title', 'Accommodation Hotels')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-summit">Hotels & Estimated Rates</h2>
            <p class="text-sm text-gray-500 mt-1">Admin view of partner hotels shown to guests for accommodation planning.</p>
        </div>
        <a href="{{ route('admin.hotels.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition whitespace-nowrap">
            + Add Hotel
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-5">
        <div class="flex gap-4 overflow-x-auto pb-2" style="scroll-snap-type:x mandatory;">
            @forelse($hotels as $hotel)
            <article class="min-w-[320px] max-w-[320px] border border-gray-200 rounded-2xl overflow-hidden shadow-sm bg-white" style="scroll-snap-align:start;">
                <div class="h-36 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center border-b border-gray-100">
                    <span class="text-5xl" aria-hidden="true">🏨</span>
                </div>
                <div class="p-4">
                    <h3 class="font-extrabold text-summit text-base">{{ $hotel->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $hotel->description ?: 'Recommended accommodation near the summit venue.' }}</p>

                    <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                        <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-2">
                            <div class="text-gray-500">Single / night</div>
                            <div class="font-bold text-summit">${{ number_format($hotel->single_price_usd) }}</div>
                            <div class="text-[11px] text-gray-500">UGX {{ number_format($hotel->single_price_ugx) }}</div>
                        </div>
                        <div class="rounded-lg bg-blue-50 border border-blue-200 p-2">
                            <div class="text-gray-500">Double / night</div>
                            <div class="font-bold text-summit">${{ number_format($hotel->double_price_usd) }}</div>
                            <div class="text-[11px] text-gray-500">UGX {{ number_format($hotel->double_price_ugx) }}</div>
                        </div>
                    </div>

                    @if($hotel->booking_url)
                    <a href="{{ $hotel->booking_url }}" target="_blank" rel="noopener"
                       class="mt-4 inline-block text-xs font-bold text-blue-600 hover:underline">
                        View Hotel Site ↗
                    </a>
                    @endif
                </div>
            </article>
            @empty
            <div class="text-sm text-gray-500">No hotels configured yet.</div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left">Hotel</th>
                    <th class="px-4 py-3 text-left">Single / Night</th>
                    <th class="px-4 py-3 text-left">Double / Night</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($hotels as $hotel)
                <tr>
                    <td class="px-4 py-3 font-semibold text-summit">{{ $hotel->name }}</td>
                    <td class="px-4 py-3">${{ number_format($hotel->single_price_usd) }} / UGX {{ number_format($hotel->single_price_ugx) }}</td>
                    <td class="px-4 py-3">${{ number_format($hotel->double_price_usd) }} / UGX {{ number_format($hotel->double_price_ugx) }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $hotel->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $hotel->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ route('admin.hotels.edit', $hotel) }}"
                               style="display:inline-block;background:#1a2a4a;color:#fff;font-size:.75rem;font-weight:700;padding:.375rem .75rem;border-radius:.5rem;text-decoration:none;"
                               onmouseover="this.style.background='#0f1f3d'" onmouseout="this.style.background='#1a2a4a'">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.hotels.destroy', $hotel) }}" onsubmit="return confirm('Delete this hotel? Existing registrations will keep their saved hotel name.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:#7f1d1d;color:#fff;font-size:.75rem;font-weight:700;padding:.375rem .75rem;border-radius:.5rem;border:none;cursor:pointer;" onmouseover="this.style.background='#450a0a'" onmouseout="this.style.background='#7f1d1d'">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-gray-400">No hotels found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
