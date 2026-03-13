@extends('layouts.public')
@section('title', 'QR Verification | Renewal Summit 2026')

@section('content')
<div class="max-w-md mx-auto px-4 py-16 text-center">

    @if($reg)
    {{-- Valid QR --}}
    <div class="bg-white rounded-2xl shadow-lg p-10">
        <div class="text-7xl mb-4">{{ $reg->isCheckedIn() ? '✅' : '🎫' }}</div>
        <h1 class="text-2xl font-extrabold text-green-600 mb-2">
            {{ $reg->isCheckedIn() ? 'Already Checked In' : 'Valid Registration' }}
        </h1>

        <div class="bg-summit text-white rounded-2xl p-6 my-6 text-left text-sm space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-400">Reference</span>
                <span class="font-bold text-yellow-400">{{ $reg->reference }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Name</span>
                <span class="font-semibold">{{ $reg->full_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Phone</span>
                <span class="font-semibold">{{ $reg->phone }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Type</span>
                @php
                    $vTypeColor = match($reg->country_type) {
                        'local'         => 'bg-gray-500',
                        'africa'        => 'bg-orange-500',
                        'international' => 'bg-blue-500',
                        default         => 'bg-gray-500',
                    };
                    $vTypeLabel = match($reg->country_type) {
                        'local'         => 'Ugandan',
                        'africa'        => 'Africa',
                        'international' => 'International',
                        default         => $reg->country_type,
                    };
                @endphp
                <span class="font-semibold uppercase text-xs px-2 py-0.5 rounded {{ $vTypeColor }} text-white">
                    {{ $vTypeLabel }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Affiliation</span>
                <span class="font-semibold uppercase">{{ $reg->affiliation }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Status</span>
                <span class="font-bold {{ $reg->isCheckedIn() ? 'text-green-400' : 'text-yellow-400' }} uppercase">
                    {{ $reg->status }}
                </span>
            </div>
            @if($reg->checked_in_at)
            <div class="flex justify-between">
                <span class="text-gray-400">Checked In</span>
                <span class="font-semibold">{{ $reg->checked_in_at->format('D d M Y H:i') }}</span>
            </div>
            @endif
        </div>

        @if(! $reg->isCheckedIn())
        <div class="text-sm text-gray-500">
            This is a valid registration. Admin check-in is done via the admin scanner.
        </div>
        @endif
    </div>

    @else
    {{-- Invalid QR --}}
    <div class="bg-white rounded-2xl shadow-lg p-10">
        <div class="text-7xl mb-4">❌</div>
        <h1 class="text-2xl font-extrabold text-red-600 mb-2">Invalid QR Code</h1>
        <p class="text-gray-500 mb-6">
            This QR code is not valid or the registration has not been confirmed.
        </p>
        <a href="{{ route('home') }}"
           class="block w-full bg-summit text-white font-bold py-3 rounded-xl">← Back to Home</a>
    </div>
    @endif

</div>
@endsection
