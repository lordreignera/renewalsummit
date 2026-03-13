@extends('layouts.admin')
@section('title', 'Registrations')
@section('page-title', 'Registrations')

@section('content')

{{-- ── Filters ─────────────────────────────────────────────────── --}}
<form method="GET" class="bg-white rounded-2xl shadow-sm p-5 mb-6">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="🔍 Search name / phone / ref..."
               class="col-span-2 border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">

        <select name="status"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            <option value="">All Statuses</option>
            @foreach(['draft','pending','paid','checked_in','cancelled'] as $s)
                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>

        <select name="affiliation"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            <option value="">All Affiliations</option>
            <option value="fcc"   {{ request('affiliation') == 'fcc'   ? 'selected' : '' }}>FCC</option>
            <option value="other" {{ request('affiliation') == 'other' ? 'selected' : '' }}>Other / Guest</option>
        </select>

        <select name="country_type"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
            <option value="">All Types</option>
            <option value="local"         {{ request('country_type') == 'local'         ? 'selected' : '' }}>🇺🇬 Ugandan</option>
            <option value="africa"        {{ request('country_type') == 'africa'        ? 'selected' : '' }}>🌍 Rest of Africa</option>
            <option value="international" {{ request('country_type') == 'international' ? 'selected' : '' }}>✈️ International</option>
        </select>

        <input type="date" name="date" value="{{ request('date') }}"
               class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none">

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 bg-gold hover:bg-yellow-600 text-white font-bold py-2 rounded-xl text-sm transition">
                Filter
            </button>
            <a href="{{ route('admin.registrations.index') }}"
               class="px-3 py-2 border border-gray-300 rounded-xl text-sm text-gray-600 hover:bg-gray-50">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- ── Toolbar ─────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-4">
    <p class="text-sm text-gray-500">
        Showing <strong>{{ $registrations->count() }}</strong> of
        <strong>{{ $registrations->total() }}</strong> registrations
    </p>
    <a href="{{ route('admin.registrations.export', request()->query()) }}"
       class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
        📥 Export CSV
    </a>
</div>

{{-- ── Table ────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Reference</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Name</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Phone</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Type</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Affil.</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Designation</th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Date</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($registrations as $reg)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-mono text-xs font-bold text-gold">{{ $reg->reference }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $reg->full_name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $reg->phone }}</td>
                    <td class="px-4 py-3">
                        @php
                            $typeColor = match($reg->country_type) {
                                'local'         => 'bg-gray-100 text-gray-600',
                                'africa'        => 'bg-orange-100 text-orange-700',
                                'international' => 'bg-blue-100 text-blue-700',
                                default         => 'bg-gray-100 text-gray-500',
                            };
                            $typeLabel = match($reg->country_type) {
                                'local'         => 'Ugandan',
                                'africa'        => 'Africa',
                                'international' => 'International',
                                default         => $reg->country_type,
                            };
                        @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $typeColor }}">
                            {{ $typeLabel }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold
                            {{ $reg->affiliation === 'fcc' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ strtoupper($reg->affiliation) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600 capitalize">
                        {{ str_replace('_', ' ', $reg->designation ?? '—') }}
                    </td>
                    <td class="px-4 py-3">
                        @php $colors = [
                            'draft'      => 'bg-gray-100 text-gray-600',
                            'pending'    => 'bg-yellow-100 text-yellow-700',
                            'paid'       => 'bg-green-100 text-green-700',
                            'checked_in' => 'bg-blue-100 text-blue-700',
                            'cancelled'  => 'bg-red-100 text-red-600',
                        ]; @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $colors[$reg->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst(str_replace('_', ' ', $reg->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $reg->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.registrations.show', $reg) }}"
                           class="text-xs bg-summit text-white px-3 py-1.5 rounded-lg hover:opacity-80 transition">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-4 py-12 text-center text-gray-400">No registrations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($registrations->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $registrations->links() }}
    </div>
    @endif
</div>

@endsection
