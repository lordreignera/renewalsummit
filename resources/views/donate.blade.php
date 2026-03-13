@extends('layouts.public')
@section('title', 'Donate | Renewal Summit 2026')

@section('content')
<div class="max-w-xl mx-auto px-4 py-16">

    <div class="text-center mb-10">
        <div class="text-6xl mb-4">🙏</div>
        <h1 class="text-3xl font-extrabold text-summit">Support Renewal Summit 2026</h1>
        <p class="text-gray-500 mt-2 leading-relaxed">
            Your donation helps cover costs for delegates from underserved regions,
            event logistics, and the overall success of this global gathering.
        </p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6 text-sm">
            <ul class="list-disc ml-4 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg p-8">
        <form method="POST" action="{{ route('donate.store') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="donor_name" value="{{ old('donor_name') }}"
                       placeholder="Your name"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition"
                       required>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" value="{{ old('phone') }}"
                       placeholder="e.g. 0772123456"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition"
                       required>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email (optional)</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="For donation receipt"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
            </div>

            {{-- Amount quick-select --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Donation Amount (UGX) <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-2 mb-3">
                    @foreach([50000, 100000, 200000, 500000, 1000000] as $preset)
                        <button type="button"
                                onclick="document.getElementById('amount-input').value='{{ $preset }}'"
                                class="border border-gray-300 rounded-xl py-2 text-sm font-medium hover:border-yellow-400 hover:bg-yellow-50 transition">
                            UGX {{ number_format($preset) }}
                        </button>
                    @endforeach
                    <button type="button"
                            onclick="document.getElementById('amount-input').focus()"
                            class="border border-yellow-400 bg-yellow-50 rounded-xl py-2 text-sm font-medium text-yellow-700">
                        Custom
                    </button>
                </div>
                <input type="number" name="amount" id="amount-input" value="{{ old('amount') }}"
                       placeholder="Enter amount in UGX (min 5,000)"
                       min="5000"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition"
                       required>
            </div>

            {{-- Payment Method --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="payment_method" value="mobile_money" class="sr-only peer" checked>
                        <div class="border-2 rounded-xl px-4 py-3 text-sm text-center transition
                                    peer-checked:border-yellow-400 peer-checked:bg-yellow-50 border-gray-200 hover:border-gray-400">
                            <div class="text-xl mb-0.5">📱</div>
                            <div class="font-bold text-xs">Mobile Money</div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="payment_method" value="visa" class="sr-only peer">
                        <div class="border-2 rounded-xl px-4 py-3 text-sm text-center transition
                                    peer-checked:border-yellow-400 peer-checked:bg-yellow-50 border-gray-200 hover:border-gray-400">
                            <div class="text-xl mb-0.5">💳</div>
                            <div class="font-bold text-xs">VISA / Card</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-5" id="network-field">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Network</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach(['MTN' => '🟡 MTN', 'AIRTEL' => '🔴 Airtel'] as $val => $label)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="network" value="{{ $val }}" class="sr-only peer"
                               {{ $val === 'MTN' ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl px-3 py-2 text-sm font-medium text-center transition
                                    peer-checked:border-yellow-400 peer-checked:bg-yellow-50 border-gray-200 hover:border-gray-400">
                            {{ $label }}
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Message (optional)</label>
                <textarea name="message" rows="2"
                          placeholder="A short note with your donation..."
                          class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition resize-none">{{ old('message') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full bg-gold hover:bg-yellow-600 text-white font-bold py-4 rounded-xl transition shadow text-lg">
                🙏 Donate Now
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('[name="payment_method"]').forEach(function(el) {
        el.addEventListener('change', function() {
            document.getElementById('network-field').classList.toggle('hidden', this.value !== 'mobile_money');
        });
    });
</script>
@endpush
@endsection
