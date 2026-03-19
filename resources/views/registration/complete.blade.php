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

        <a href="{{ route('register.accommodation', ['reference' => $reg->reference, 'token' => $reg->qr_token]) }}"
           class="block w-full bg-gold hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition mb-3">
            🏨 Continue to Accommodation Planning
        </a>

        @if($reg->accommodation_hotel_id)
        <div class="text-xs text-gray-500 bg-gray-50 rounded-xl p-3 mb-3 text-left">
            <div><strong>Accommodation:</strong> {{ $reg->accommodation_choice }}</div>
            <div><strong>Mode:</strong> {{ ucfirst(str_replace('_', ' ', $reg->accommodation_booking_mode ?? 'self_book')) }}</div>
            <div><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $reg->accommodation_payment_status ?? 'not_required')) }}</div>
        </div>
        @endif

        <a href="{{ route('home') }}"
           class="block w-full bg-summit hover:bg-blue-900 text-white font-bold py-3 rounded-xl transition">
            ← Back to Home
        </a>
    </div>

    {{-- Accommodation next-step note --}}
    <div class="bg-white rounded-2xl shadow-lg p-8 mt-6">
        <h2 class="text-xl font-extrabold text-summit mb-2">🏨 Accommodation Next Step</h2>
        <p class="text-sm text-gray-500 mb-4">
            Use the accommodation planner to choose your hotel, room type, and number of nights.
            You can either book yourself, request us to book and pay later, or pay accommodation through us now.
        </p>
        <a href="{{ route('register.accommodation', ['reference' => $reg->reference, 'token' => $reg->qr_token]) }}"
           class="inline-block bg-gold hover:bg-yellow-600 text-white font-bold px-5 py-3 rounded-xl transition text-sm">
            Open Accommodation Planner →
        </a>
    </div>
</div>
@endsection
