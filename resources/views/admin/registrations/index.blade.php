@extends('layouts.admin')
@section('title', 'Registrations')
@section('page-title', 'Registrations')

@section('content')

{{-- ── KPI Cards ──────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">

    {{-- Total Registered --}}
    <a href="{{ route('admin.registrations.index', array_merge(request()->except('acc_status','page'), [])) }}"
       class="bg-white rounded-2xl shadow-sm p-5 flex flex-col gap-1 border-t-4 border-gray-400 hover:shadow-md transition group {{ !request('acc_status') ? 'ring-2 ring-gray-300' : '' }}">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Registered</span>
        <span class="text-3xl font-extrabold text-gray-800 group-hover:text-gray-900">{{ number_format($kpiTotal) }}</span>
        <span class="text-xs text-gray-400">All registrations</span>
    </a>

    {{-- Fully Paid (Both) --}}
    <a href="{{ route('admin.registrations.index', array_merge(request()->except('acc_status','page'), ['acc_status' => 'fully_paid'])) }}"
       class="bg-white rounded-2xl shadow-sm p-5 flex flex-col gap-1 border-t-4 border-green-500 hover:shadow-md transition group {{ request('acc_status') === 'fully_paid' ? 'ring-2 ring-green-400' : '' }}">
        <span class="text-xs font-semibold text-green-600 uppercase tracking-wide">Fully Paid</span>
        <span class="text-3xl font-extrabold text-green-700">{{ number_format($kpiFullyPaid) }}</span>
        <span class="text-xs text-gray-400">Reg ✓ &amp; Acc ✓</span>
    </a>

    {{-- Reg Paid · Acc Still Pending --}}
    <a href="{{ route('admin.registrations.index', array_merge(request()->except('acc_status','page'), ['acc_status' => 'reg_paid_acc_pending'])) }}"
       class="bg-white rounded-2xl shadow-sm p-5 flex flex-col gap-1 border-t-4 border-orange-400 hover:shadow-md transition group {{ request('acc_status') === 'reg_paid_acc_pending' ? 'ring-2 ring-orange-400' : '' }}">
        <span class="text-xs font-semibold text-orange-600 uppercase tracking-wide">Reg Paid · Acc Pending</span>
        <span class="text-3xl font-extrabold text-orange-600">{{ number_format($kpiRegPaidAccPending) }}</span>
        <span class="text-xs text-gray-400">Awaiting acc payment</span>
    </a>

    {{-- Awaiting Registration Payment --}}
    <a href="{{ route('admin.registrations.index', array_merge(request()->except('acc_status','page'), ['acc_status' => 'awaiting_reg'])) }}"
       class="bg-white rounded-2xl shadow-sm p-5 flex flex-col gap-1 border-t-4 border-red-400 hover:shadow-md transition group {{ request('acc_status') === 'awaiting_reg' ? 'ring-2 ring-red-400' : '' }}">
        <span class="text-xs font-semibold text-red-600 uppercase tracking-wide">Awaiting Reg Payment</span>
        <span class="text-3xl font-extrabold text-red-600">{{ number_format($kpiAwaitingReg) }}</span>
        <span class="text-xs text-gray-400">Draft / Pending</span>
    </a>

    {{-- Checked In --}}
    <a href="{{ route('admin.registrations.index', array_merge(request()->except('acc_status','page'), ['acc_status' => 'checked_in'])) }}"
       class="bg-white rounded-2xl shadow-sm p-5 flex flex-col gap-1 border-t-4 border-blue-500 hover:shadow-md transition group {{ request('acc_status') === 'checked_in' ? 'ring-2 ring-blue-400' : '' }}">
        <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Checked In</span>
        <span class="text-3xl font-extrabold text-blue-700">{{ number_format($kpiCheckedIn) }}</span>
        <span class="text-xs text-gray-400">At the venue</span>
    </a>

</div>

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
            <option value="other" {{ request('affiliation') == 'other' ? 'selected' : '' }}>Guest</option>
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

        <select name="acc_mode"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none
                       {{ request('acc_mode') ? 'border-teal-400 bg-teal-50' : '' }}">
            <option value="">All Accommodation Types</option>
            <option value="book_through_us" {{ request('acc_mode') == 'book_through_us' ? 'selected' : '' }}>🏨 Booked Through Us</option>
            <option value="self_book"       {{ request('acc_mode') == 'self_book'       ? 'selected' : '' }}>🔑 Self Booking</option>
            <option value="none"            {{ request('acc_mode') == 'none'            ? 'selected' : '' }}>— No Accommodation</option>
        </select>

        <select name="acc_status"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-400 outline-none
                       {{ request('acc_status') ? 'border-yellow-400 bg-yellow-50' : '' }}">
            <option value="">All Payment States</option>
            <option value="fully_paid"           {{ request('acc_status') == 'fully_paid'           ? 'selected' : '' }}>✅ Fully Paid (Reg + Acc)</option>
            <option value="reg_paid_acc_pending"  {{ request('acc_status') == 'reg_paid_acc_pending'  ? 'selected' : '' }}>⏳ Reg Paid · Acc Pending</option>
            <option value="awaiting_reg"          {{ request('acc_status') == 'awaiting_reg'          ? 'selected' : '' }}>🔴 Awaiting Reg Payment</option>
            <option value="checked_in"            {{ request('acc_status') == 'checked_in'            ? 'selected' : '' }}>🏷️ Checked In</option>
        </select>

        <div class="flex gap-2">
            <button type="submit"
                    class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded-xl text-sm transition">
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
    <div class="flex items-center gap-2">
        @if(auth()->user()->hasRole('super_admin', 'registrar'))
          <a href="{{ route('admin.registrations.create') }}"
              class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
            + Manual Registration
        </a>
        @endif
        @if(auth()->user()->hasRole('super_admin', 'finance'))
        <a href="{{ route('admin.registrations.export', request()->query()) }}"
           class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
            Export CSV
        </a>
        @endif
    </div>
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
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Accommodation</th>

                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Payment Status</th>
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
                        @php $affLabel = $reg->affiliation === 'fcc' ? 'FCC' : 'Guest'; @endphp
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold
                            {{ $reg->affiliation === 'fcc' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ strtoupper($affLabel) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600 capitalize">
                        {{ str_replace('_', ' ', $reg->designation ?? '—') }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        @if($reg->accommodation_hotel_id)
                            <div class="font-semibold text-summit">{{ $reg->accommodationHotel->name ?? $reg->accommodation_choice }}</div>
                            <div>{{ ucfirst(str_replace('_',' ', $reg->accommodation_booking_mode ?? 'self_book')) }}</div>
                            <div class="text-gray-400">{{ ucfirst($reg->accommodation_room_type ?? 'single') }} · {{ (int) ($reg->accommodation_nights ?? 1) }} night(s)</div>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @php
                            $regColors = [
                                'draft'      => 'bg-gray-100 text-gray-600',
                                'pending'    => 'bg-yellow-100 text-yellow-700',
                                'paid'       => 'bg-green-100 text-green-700',
                                'checked_in' => 'bg-blue-100 text-blue-700',
                                'cancelled'  => 'bg-red-100 text-red-600',
                            ];

                            $accStatus = 'not_selected';
                            $accLabel = 'Not selected';
                            if ($reg->accommodation_hotel_id) {
                                if (($reg->accommodation_booking_mode ?? '') === 'self_book') {
                                    $accStatus = 'self_book';
                                    $accLabel = 'Self book';
                                } elseif (($reg->accommodation_booking_mode ?? '') === 'book_through_us_no_payment') {
                                    $accStatus = 'pending_manual';
                                    $accLabel = 'Booked via us (pay later)';
                                } else {
                                    $accStatus = $reg->accommodation_payment_status ?: 'pending';
                                    $accLabel = ucfirst(str_replace('_', ' ', $accStatus));
                                }
                            }

                            $accColors = [
                                'not_selected' => 'bg-gray-100 text-gray-500',
                                'self_book' => 'bg-indigo-100 text-indigo-700',
                                'pending_manual' => 'bg-orange-100 text-orange-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'paid' => 'bg-green-100 text-green-700',
                                'not_required' => 'bg-gray-100 text-gray-500',
                            ];
                        @endphp

                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-semibold text-gray-500">Reg:</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $regColors[$reg->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst(str_replace('_', ' ', $reg->status)) }}
                                </span>
                                <span class="text-[11px] text-gray-400">{{ $reg->currency }} {{ number_format($reg->total_amount) }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-semibold text-gray-500">Acc:</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $accColors[$accStatus] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $accLabel }}
                                </span>
                                @if($reg->accommodation_hotel_id && $reg->accommodation_fee)
                                    <span class="text-[11px] text-gray-400">{{ $reg->accommodation_currency ?: $reg->currency }} {{ number_format($reg->accommodation_fee) }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400">{{ $reg->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2 whitespace-nowrap">
                            <a href="{{ route('admin.registrations.show', $reg) }}"
                                         class="inline-flex items-center justify-center text-xs font-bold bg-gray-800 hover:bg-gray-900 text-white px-3 py-2 rounded-lg transition shadow-sm">
                                View
                            </a>

                            @if(in_array($reg->status, ['paid', 'checked_in']))
                                <a href="{{ route('register.accommodation', ['reference' => $reg->reference, 'token' => $reg->qr_token]) }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center text-xs font-bold bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg transition shadow-sm">
                                    Hotel
                                </a>
                            @endif

                            @if(in_array($reg->status, ['draft', 'pending']))
                                <a href="{{ route('admin.registrations.payment', $reg) }}"
                                   class="inline-flex items-center justify-center text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition shadow-sm">
                                    Pay
                                </a>
                            @endif
                        </div>
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
