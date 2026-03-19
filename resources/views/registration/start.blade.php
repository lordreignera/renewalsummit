@extends('layouts.public')
@section('title', 'Register – Renewal Summit 2026')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-16">

    <div class="text-center mb-10">
        <h1 class="text-3xl font-extrabold text-summit">Register for Renewal Summit 2026</h1>
        <p class="text-gray-500 mt-2">Secure your place · August 17–21, 2026 · Ggaba Community Church, Uganda</p>
    </div>

    {{-- Start New --}}
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
        <h2 class="text-xl font-bold text-summit mb-1">New Registration</h2>
        <p class="text-sm text-gray-500 mb-6">Fill in the 3-step form. Payment is processed at the final step.</p>
        <a href="{{ route('register.step1') }}"
           class="block w-full bg-gold hover:bg-yellow-600 text-white text-center font-bold py-3 rounded-xl
                  text-lg transition shadow">
            Start Registration →
        </a>
    </div>

    {{-- Resume --}}
    <div class="bg-white rounded-2xl shadow-lg p-8" id="resume">
        <h2 class="text-xl font-bold text-summit mb-1">Resume Registration</h2>
        <p class="text-sm text-gray-500 mb-6">Already started or already paid? Enter your phone number to continue your registration or jump to accommodation planning.</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-3 mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.resume') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
                <input type="tel" name="phone" placeholder="e.g. 0772123456"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2
                              focus:ring-yellow-400 focus:border-yellow-400 outline-none transition"
                       required>
            </div>
            <button type="submit"
                    class="w-full bg-summit hover:bg-blue-900 text-white font-bold py-3 rounded-xl transition">
                Resume →
            </button>
        </form>
    </div>

    {{-- Info --}}
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-xl p-5 text-sm text-yellow-800">
        <p class="font-semibold mb-1">💡 What to expect:</p>
        <ol class="list-decimal ml-4 space-y-1">
            <li>Enter your personal details &amp; designation</li>
            <li>Provide your church / FCC affiliation</li>
            <li>Complete payment via Mobile Money or VISA</li>
            <li>Receive your QR entry code via email</li>
            <li>Optionally continue to accommodation planning</li>
        </ol>
    </div>
</div>
@endsection
