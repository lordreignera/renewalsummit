@extends('layouts.public')
@section('title', 'Step 3 – Payment | Renewal Summit 2026')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    @include('registration.partials.steps', ['currentStep' => 3])

    <div class="bg-white rounded-2xl shadow-lg p-5 sm:p-8">
        <h2 class="text-2xl font-extrabold text-summit mb-1">Step 3: Payment</h2>
        <p class="text-sm text-gray-500 mb-6">Complete payment to confirm your registration.</p>

        {{-- Accommodation notice --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3 mb-6 text-sm flex gap-3 items-start">
            <span class="text-xl leading-none">🏨</span>
            <div>
                <span class="font-bold">Need accommodation?</span>
                Accommodation is not included in the registration fee. Visit our
                <a href="{{ route('home') }}#accommodation" target="_blank"
                   class="underline font-semibold hover:text-blue-600">hotel listings page</a>
                to book directly with recommended hotels near the venue.
            </div>
        </div>

        {{-- Order summary --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 sm:p-5 mb-6 text-sm">
            <h3 class="font-bold text-summit mb-3">Registration Summary</h3>
            <div class="space-y-2">
                <div class="flex justify-between gap-2">
                    <span class="text-gray-600 shrink-0">Name</span>
                    <span class="font-semibold text-right">{{ $reg->full_name }}</span>
                </div>
                @php
                $designationLabels = [
                    'fcc_regional_leader' => 'FCC Regional Leader',
                    'senior_pastor'       => 'Senior Pastor',
                    'church_leader'       => 'Church Leader' . ($reg->designation_specify ? " – {$reg->designation_specify}" : ''),
                    'corporate'           => 'Corporate / Organisation',
                ];
                @endphp
                <div class="flex justify-between gap-2">
                    <span class="text-gray-600 shrink-0">Designation</span>
                    <span class="font-semibold text-right">{{ $designationLabels[$reg->designation] ?? $reg->designation }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-gray-600 shrink-0">Phone</span>
                    <span class="font-semibold text-right">{{ $reg->phone }}</span>
                </div>
                @if($reg->email)
                <div class="flex justify-between gap-2">
                    <span class="text-gray-600 shrink-0">Email</span>
                    <span class="font-semibold text-right break-all">{{ $reg->email }}</span>
                </div>
                @endif
                <div class="flex justify-between gap-2">
                    <span class="text-gray-600 shrink-0">Attendee Type</span>
                    <span class="font-semibold text-right">{{ $reg->country_type === 'local' ? 'Ugandan' : ($reg->country_type === 'africa' ? 'Rest of Africa' : 'International') }}</span>
                </div>
                <div class="flex justify-between gap-2">
                    <span class="text-gray-600 shrink-0">Affiliation</span>
                    <span class="font-semibold uppercase text-right">{{ $reg->affiliation === 'fcc' ? 'FCC Member' : 'Other / Guest' }}</span>
                </div>
                @php
                $feeDisplay = $reg->currency === 'USD'
                    ? '$' . number_format($reg->total_amount) . ' USD'
                    : 'UGX ' . number_format($reg->total_amount);
                @endphp
                <div class="border-t border-gray-300 pt-2 flex justify-between gap-2">
                    <span class="font-bold text-summit text-base shrink-0">Registration Fee</span>
                    <span class="font-extrabold text-gold text-xl text-right">{{ $feeDisplay }}</span>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-5 text-sm">
                <ul class="list-disc ml-4 space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.step3.pay') }}" id="payment-form">
            @csrf

            {{-- Payment Method --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-3">Payment Method <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="payment_method" value="mobile_money" class="sr-only peer" checked
                               id="pm-mm">
                        <div class="border-2 rounded-xl px-4 py-4 text-center transition
                                    peer-checked:border-yellow-400 peer-checked:bg-yellow-50
                                    border-gray-200 hover:border-gray-400">
                            <div class="text-2xl mb-1">📱</div>
                            <div class="font-bold text-sm">Mobile Money</div>
                            <div class="text-xs text-gray-400 mt-1">MTN / Airtel</div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="payment_method" value="visa" class="sr-only peer" id="pm-visa">
                        <div class="border-2 rounded-xl px-4 py-4 text-center transition
                                    peer-checked:border-yellow-400 peer-checked:bg-yellow-50
                                    border-gray-200 hover:border-gray-400">
                            <div class="text-2xl mb-1">💳</div>
                            <div class="font-bold text-sm">VISA / Card</div>
                            <div class="text-xs text-gray-400 mt-1">Online payment</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Mobile Money fields --}}
            <div id="mm-fields">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Network <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach(['MTN' => '🟡 MTN Mobile Money', 'AIRTEL' => '🔴 Airtel Money'] as $val => $label)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="network" value="{{ $val }}" class="sr-only peer"
                                   {{ $val === 'MTN' ? 'checked' : '' }}>
                            <div class="border-2 rounded-xl px-3 py-2 text-sm font-medium text-center transition
                                        peer-checked:border-yellow-400 peer-checked:bg-yellow-50
                                        border-gray-200 hover:border-gray-400">
                                {{ $label }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mobile Money Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone_number" value="{{ old('phone_number', $reg->phone) }}"
                           placeholder="e.g. 0772123456"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2
                                  focus:ring-yellow-400 outline-none transition">
                    <p class="text-xs text-gray-400 mt-1">You will receive a payment prompt on this number.</p>
                </div>
            </div>

            {{-- VISA info --}}
            <div id="visa-info" class="hidden mb-5 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3 text-sm">
                💳 You will be redirected to a secure payment page to complete your VISA card payment.
            </div>

            {{-- T&C --}}
            <div class="mb-6 flex items-start gap-3">
                <input type="checkbox" id="agree" required
                       class="mt-0.5 accent-yellow-500 w-4 h-4 shrink-0">
                <label for="agree" class="text-xs text-gray-600">
                    I confirm that the details provided are correct and I agree to the terms and conditions of
                    Renewal Summit 2026. I understand that registration is confirmed only upon successful payment.
                </label>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('register.step2') }}"
                   class="flex-1 border-2 border-gray-300 text-gray-600 font-bold py-3 rounded-xl text-center transition hover:bg-gray-50">
                    ← Back
                </a>
                <button type="submit" id="pay-btn"
                        class="flex-grow bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition shadow text-lg">
                    💳 Pay {{ $reg->currency === 'USD' ? '$' . number_format($reg->total_amount) . ' USD' : 'UGX ' . number_format($reg->total_amount) }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('[name="payment_method"]').forEach(function(el) {
        el.addEventListener('change', function() {
            const isMM = this.value === 'mobile_money';
            document.getElementById('mm-fields').classList.toggle('hidden', !isMM);
            document.getElementById('visa-info').classList.toggle('hidden', isMM);
        });
    });

    document.getElementById('payment-form').addEventListener('submit', function() {
        const btn = document.getElementById('pay-btn');
        btn.disabled = true;
        btn.textContent = 'Processing...';
    });
</script>
@endpush
@endsection
