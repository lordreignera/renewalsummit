@extends('layouts.public')
@section('title', 'Step 3 – Payment | Renewal Summit 2026')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    @php $embed = request()->boolean('embed'); @endphp

    @include('registration.partials.steps', ['currentStep' => 3])

    <div class="bg-white rounded-2xl shadow-lg p-5 sm:p-8">
        <h2 class="text-2xl font-extrabold text-summit mb-1">Step 3: Payment</h2>
        <p class="text-sm text-gray-500 mb-6">Complete payment to confirm your registration.</p>

        {{-- Accommodation notice --}}
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3 mb-6 text-sm flex gap-3 items-start">
            <span class="text-xl leading-none">🏨</span>
            <div>
                <span class="font-bold">Need accommodation?</span>
                After registration payment is successful, you will continue to the accommodation planner
                where you can choose a hotel, room type, nights, and decide whether to pay through us.
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

        @if($reg->country_type !== 'local')

            {{-- Contact Us notice for non-Uganda delegates --}}
            <div class="bg-amber-50 border-2 border-amber-300 rounded-xl px-5 py-6 mb-6 text-center">
                <div class="text-4xl mb-3">📞</div>
                <h3 class="text-lg font-extrabold text-amber-800 mb-2">Online Payment Coming Soon</h3>
                <p class="text-sm text-amber-700 mb-4">
                    International and Rest-of-Africa card payments are not yet available online.
                    Please contact us directly to complete your registration payment.
                </p>
                <div class="space-y-3">
                    <a href="tel:+256772120855"
                       class="flex items-center justify-center gap-2 bg-white border-2 border-amber-400 hover:bg-amber-100 text-amber-900 font-bold rounded-xl px-5 py-3 transition text-sm">
                        📱 +256 772 120 855
                    </a>
                    <a href="mailto:renewalsummit@africarenewal.org"
                       class="flex items-center justify-center gap-2 bg-white border-2 border-amber-400 hover:bg-amber-100 text-amber-900 font-bold rounded-xl px-5 py-3 transition text-sm break-all">
                        ✉️ renewalsummit@africarenewal.org
                    </a>
                </div>
                <p class="text-xs text-gray-500 mt-4">Our team will guide you through payment and confirm your registration.</p>
            </div>

            <a href="{{ route('register.step2', $embed ? ['embed' => 1] : []) }}"
               class="block w-full border-2 border-gray-300 text-gray-600 font-bold py-3 rounded-xl text-center transition hover:bg-gray-50">
                ← Back
            </a>

        @else

        <form method="POST" action="{{ route('register.step3.pay', $embed ? ['embed' => 1] : []) }}" id="payment-form">
            @csrf

            {{-- Mobile Money fields --}}
            <div id="mm-fields">
                <input type="hidden" name="payment_method" value="mobile_money">
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
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Mobile Money Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="phone_number" value="{{ old('phone_number', $reg->phone) }}"
                           placeholder="e.g. 0772123456"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2
                                  focus:ring-yellow-400 outline-none transition">
                    <p class="text-xs text-gray-500 mt-1">
                        Pre-filled from your registration number.
                        <span class="font-semibold text-gray-700">You can change this to any other mobile money number</span>
                        — the pay prompt will be sent to whichever number you enter here.
                    </p>
                </div>
                {{-- USSD prompt notice --}}
                <div class="bg-amber-50 border border-amber-300 rounded-xl px-4 py-3 mb-3 text-sm text-amber-800 flex gap-3 items-start">
                    <span class="text-xl leading-none flex-shrink-0">📲</span>
                    <div>
                        <span class="font-bold">You will receive a USSD prompt</span> on your phone from
                        <strong>Swapp Payment Systems</strong>. When it appears, enter your
                        <strong>mobile money PIN</strong> to complete the payment.
                        <span class="block text-amber-700 text-xs mt-1">Do not close the prompt — it will disappear automatically once payment is confirmed.</span>
                    </div>
                </div>
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
                <a href="{{ route('register.step2', $embed ? ['embed' => 1] : []) }}"
                   class="flex-1 border-2 border-gray-300 text-gray-600 font-bold py-3 rounded-xl text-center transition hover:bg-gray-50">
                    ← Back
                </a>
                <button type="submit" id="pay-btn"
                        class="flex-grow bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition shadow text-lg">
                    💳 Pay {{ $reg->currency === 'USD' ? '$' . number_format($reg->total_amount) . ' USD' : 'UGX ' . number_format($reg->total_amount) }}
                </button>
            </div>
        </form>

        @endif
    </div>
</div>

@push('scripts')
<script>
    @if($reg->country_type === 'local')
    document.getElementById('payment-form').addEventListener('submit', function() {
        const btn = document.getElementById('pay-btn');
        btn.disabled = true;
        btn.innerHTML = '<span style="display:inline-block;animation:spin 1s linear infinite;margin-right:.4rem;">⏳</span> Processing...';
    });
    @endif
</script>
@endpush
@endsection
