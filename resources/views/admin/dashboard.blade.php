@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ── Stats Grid ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-5 mb-8">
    @foreach([
        ['Total Registered', $stats['total'],         '✅', 'bg-green-50',  'text-green-700'],
        ['Checked In',       $stats['checked_in'],    '📲', 'bg-blue-50',   'text-blue-700'],
        ['Pending Payment',  $stats['pending'],       '⏳', 'bg-yellow-50', 'text-yellow-700'],
        ['FCC Members',      $stats['fcc'],           '✝️',  'bg-purple-50', 'text-purple-700'],
        ['International',    $stats['international'], '🌍', 'bg-indigo-50', 'text-indigo-700'],
        ['Local Leaders',   $stats['local'],         '🇺🇬', 'bg-orange-50', 'text-orange-700'],
        ['Revenue (UGX)',    number_format($stats['revenue']),   '💰', 'bg-emerald-50', 'text-emerald-700'],
        ['Donations (UGX)',  number_format($stats['donations']), '🙏', 'bg-pink-50',    'text-pink-700'],
    ] as [$label, $value, $icon, $bg, $text])
    <div class="{{ $bg }} rounded-2xl p-5">
        <div class="text-3xl mb-1">{{ $icon }}</div>
        <div class="text-2xl font-extrabold {{ $text }}">{{ $value }}</div>
        <div class="text-xs text-gray-500 font-medium mt-0.5">{{ $label }}</div>
    </div>
    @endforeach
</div>

<div class="grid md:grid-cols-2 gap-6">

    {{-- ── Today's Registrations ──────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-summit">Today's New Registrations</h2>
            <span class="bg-gold text-white text-xs font-bold px-2 py-0.5 rounded">
                {{ $todayRegistrations->count() }}
            </span>
        </div>

        @if($todayRegistrations->isEmpty())
            <p class="text-gray-400 text-sm text-center py-6">No new paid registrations today.</p>
        @else
            <div class="space-y-3">
                @foreach($todayRegistrations as $reg)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl text-sm">
                    <div class="w-8 h-8 rounded-full bg-summit text-white flex items-center justify-center text-xs font-bold">
                        {{ strtoupper(substr($reg->full_name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold truncate">{{ $reg->full_name }}</div>
                        <div class="text-xs text-gray-400">{{ $reg->reference }} · {{ $reg->phone }}</div>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full font-bold
                        {{ $reg->status === 'checked_in' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                        {{ $reg->status }}
                    </span>
                </div>
                @endforeach
            </div>
            <a href="{{ route('admin.registrations.index', ['date' => today()->format('Y-m-d')]) }}"
               class="block text-center text-xs text-gold hover:underline mt-3">View all today →</a>
        @endif
    </div>

    {{-- ── 7-Day Registration Activity ────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-summit mb-4">7-Day Registration Activity</h2>
        @if($daily->isEmpty())
            <p class="text-gray-400 text-sm text-center py-6">No registrations in the last 7 days yet.</p>
        @else
            <div class="space-y-2">
                @php $maxVal = $daily->max() ?: 1; @endphp
                @foreach($daily as $date => $count)
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-xs text-gray-400 w-20">{{ \Carbon\Carbon::parse($date)->format('D d M') }}</span>
                    <div class="flex-1 bg-gray-100 rounded-full h-4 overflow-hidden">
                        <div class="bg-gold h-4 rounded-full"
                             style="width: {{ round(($count / $maxVal) * 100) }}%"></div>
                    </div>
                    <span class="font-bold text-summit w-6 text-right">{{ $count }}</span>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- ── Quick Actions ───────────────────────────────────────────── --}}
<div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
    <a href="{{ route('admin.checkin') }}"
       class="bg-summit text-white rounded-2xl p-5 text-center hover:opacity-90 transition">
        <div class="text-3xl mb-2">📲</div>
        <div class="font-bold text-sm">Open Scanner</div>
    </a>
    <a href="{{ route('admin.registrations.index') }}"
       class="bg-white border-2 border-gray-200 rounded-2xl p-5 text-center hover:border-yellow-400 transition">
        <div class="text-3xl mb-2">📋</div>
        <div class="font-bold text-sm text-summit">All Registrations</div>
    </a>
    <a href="{{ route('admin.registrations.export') }}"
       class="bg-white border-2 border-gray-200 rounded-2xl p-5 text-center hover:border-yellow-400 transition">
        <div class="text-3xl mb-2">📥</div>
        <div class="font-bold text-sm text-summit">Export CSV</div>
    </a>
    <a href="{{ route('register.start') }}" target="_blank"
       class="bg-white border-2 border-gray-200 rounded-2xl p-5 text-center hover:border-yellow-400 transition">
        <div class="text-3xl mb-2">📝</div>
        <div class="font-bold text-sm text-summit">Registration Form</div>
    </a>
</div>

@endsection
