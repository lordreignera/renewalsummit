@extends('layouts.public')
@section('title', 'Registration Complete! | Renewal Summit 2026')

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 text-center">

    <div class="bg-white rounded-2xl shadow-lg p-10">
        <div class="text-7xl mb-4">🎉</div>
        <h1 class="text-3xl font-extrabold text-green-600 mb-2">Registration Confirmed!</h1>
        <p class="text-gray-600 mb-8">
            Welcome, <strong>{{ $reg->full_name }}</strong>! Your payment was successful and your
            spot at Renewal Summit 2026 is confirmed.
        </p>

        {{-- Reference Card --}}
        <div class="bg-summit text-white rounded-2xl p-6 mb-8">
            <div class="text-xs uppercase tracking-widest text-gray-400 mb-1">Registration Reference</div>
            <div class="text-3xl font-extrabold text-yellow-400 mb-4">{{ $reg->reference }}</div>

            <div class="grid grid-cols-2 gap-3 text-sm text-left">
                <div>
                    <div class="text-gray-400 text-xs">Name</div>
                    <div class="font-semibold">{{ $reg->full_name }}</div>
                </div>
                <div>
                    <div class="text-gray-400 text-xs">Phone</div>
                    <div class="font-semibold">{{ $reg->phone }}</div>
                </div>

                <div class="col-span-2">
                    <div class="text-gray-400 text-xs">Amount Paid</div>
                    <div class="font-extrabold text-yellow-400 text-xl">{{ $reg->formattedTotal }}</div>
                </div>
            </div>
        </div>

        {{-- QR Code --}}
        @if($qrUrl)
        <div class="mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-3">📲 Your Entry QR Code</p>
            <img src="{{ $qrUrl }}" alt="QR Code"
                 style="width:192px;height:192px;display:block;margin:0 auto;"
                 class="border-4 border-yellow-400 rounded-xl">
            <p class="text-xs text-gray-400 mt-2">Present this QR code at the venue gate for check-in.</p>
        </div>
        @endif

        {{-- Email note --}}
        @if($reg->email)
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm mb-6">
            📧 A confirmation email with your QR code has been sent to <strong>{{ $reg->email }}</strong>
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl px-4 py-3 text-sm mb-6">
            💡 No email was provided. Please screenshot or save your reference number: <strong>{{ $reg->reference }}</strong>
        </div>
        @endif

        {{-- Event details --}}
        <div class="text-sm text-gray-600 bg-gray-50 rounded-xl p-4 mb-6">
            <div class="font-bold text-summit mb-2">📅 Renewal Summit 2026</div>
            <div>📍 Ggaba Community Church, Uganda</div>
            <div>August 17–21, 2026</div>
            <div>🏷 Theme: Healthy Church</div>
        </div>

        <a href="{{ route('home') }}"
           class="block w-full bg-summit hover:bg-blue-900 text-white font-bold py-3 rounded-xl transition">
            ← Back to Home
        </a>
    </div>

    {{-- Accommodation & Transport --}}
    <div class="bg-white rounded-2xl shadow-lg p-8 mt-6">
        <h2 class="text-xl font-extrabold text-summit mb-2">🏨 Do you need accommodation &amp; transport?</h2>
        <p class="text-sm text-gray-500 mb-5">
            Your registration is confirmed! Accommodation is arranged separately — please book directly
            with any of our recommended partner hotels near Ggaba Community Church.
        </p>

        @php
        $hotels = [
            ['name' => 'Speke Resort Munyonyo', 'desc' => 'Luxury lakeside resort · ~5 km from venue',  'url' => 'https://www.spekeresort.com'],
            ['name' => 'Sir Jose Hotel',        'desc' => 'UGX 120,000/night · Near venue',             'url' => 'https://www.google.com/search?q=Sir+Jose+Hotel+Kampala'],
            ['name' => 'St Mbaga Seminary',     'desc' => 'Peaceful retreat close to the venue',        'url' => 'https://www.google.com/search?q=St+Mbaga+Seminary+Kampala'],
            ['name' => 'Eka Hotel',             'desc' => 'Modern hotel in Kampala',                    'url' => 'https://www.ekahotel.com'],
        ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-5">
            @foreach($hotels as $hotel)
            <a href="{{ $hotel['url'] }}" target="_blank" rel="noopener noreferrer"
               class="flex items-center gap-3 border-2 border-gray-100 hover:border-yellow-400
                      rounded-xl px-4 py-3 transition group">
                <div class="text-2xl">🏨</div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-summit text-sm group-hover:text-gold">{{ $hotel['name'] }}</div>
                    <div class="text-xs text-gray-400">{{ $hotel['desc'] }}</div>
                </div>
                <div class="text-gray-300 group-hover:text-gold shrink-0">→</div>
            </a>
            @endforeach
        </div>

        <p class="text-xs text-gray-400 text-center">
            For transport or GCC Guest House enquiries, contact
            <a href="mailto:renewalsummit@africarenewal.org" class="underline text-gold">renewalsummit@africarenewal.org</a>
        </p>
    </div>
</div>
@endsection
