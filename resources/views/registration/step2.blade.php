@extends('layouts.public')
@section('title', 'Step 2 – Review Details | Renewal Summit 2026')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    @php $embed = request()->boolean('embed'); @endphp

    @include('registration.partials.steps', ['currentStep' => 2])

    <div class="bg-white rounded-2xl shadow-lg p-5 sm:p-8">
        <h2 class="text-2xl font-extrabold text-summit mb-1">Step 2: Review Your Details</h2>
        <p class="text-sm text-gray-500 mb-6">Please confirm your details before proceeding to payment.</p>

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-5 text-sm">{{ session('error') }}</div>
        @endif

        {{-- Summary card --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 mb-6 text-sm space-y-3">

            <div class="flex justify-between gap-2">
                <span class="text-gray-500 shrink-0">Full Name</span>
                <span class="font-semibold text-right">{{ $reg->full_name }}</span>
            </div>

            @php
            $desigLabels = [
                'fcc_regional_leader' => 'FCC Regional Leader',
                'senior_pastor'       => 'Senior Pastor',
                'church_leader'       => 'Church Leader' . ($reg->designation_specify ? " – {$reg->designation_specify}" : ''),
                'corporate'           => 'Corporate / Organisation',
            ];
            @endphp
            <div class="flex justify-between gap-2">
                <span class="text-gray-500 shrink-0">Designation</span>
                <span class="font-semibold text-right">{{ $desigLabels[$reg->designation] ?? $reg->designation }}</span>
            </div>

            <div class="flex justify-between gap-2">
                <span class="text-gray-500 shrink-0">Phone</span>
                <span class="font-semibold text-right">{{ $reg->phone }}</span>
            </div>

            @if($reg->email)
            <div class="flex justify-between gap-2">
                <span class="text-gray-500 shrink-0">Email</span>
                <span class="font-semibold text-right break-all">{{ $reg->email }}</span>
            </div>
            @endif

            <div class="flex justify-between gap-2">
                <span class="text-gray-500 shrink-0">Attending From</span>
                <span class="font-semibold text-right">
                    {{ ['local' => '🇺🇬 Uganda', 'africa' => '🌍 Rest of Africa', 'international' => '✈️ International'][$reg->country_type] ?? $reg->country_type }}
                </span>
            </div>

            @if($reg->nationality)
            <div class="flex justify-between gap-2">
                <span class="text-gray-500 shrink-0">Country of Residence</span>
                <span class="font-semibold text-right">{{ $reg->nationality }}</span>
            </div>
            @endif

            <div class="flex justify-between gap-2">
                <span class="text-gray-500 shrink-0">FCC Member</span>
                <span class="font-semibold text-right">
                    @if($reg->affiliation === 'fcc')
                        <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-0.5 rounded">Yes — FCC</span>
                    @else
                        No
                    @endif
                </span>
            </div>

            @if($reg->affiliation === 'fcc')
            <div class="bg-yellow-50 border border-yellow-100 rounded-lg p-3 space-y-1.5 text-xs">
                <div class="flex justify-between gap-2"><span class="text-gray-500">Region</span><span class="font-semibold text-right">{{ $reg->fcc_region }}</span></div>
                <div class="flex justify-between gap-2"><span class="text-gray-500">Regional Leader</span><span class="font-semibold text-right">{{ $reg->fcc_regional_leader }}</span></div>
                <div class="flex justify-between gap-2"><span class="text-gray-500">Church</span><span class="font-semibold text-right">{{ $reg->fcc_church }}</span></div>
                @if($reg->fcc_pastor)
                <div class="flex justify-between gap-2"><span class="text-gray-500">Local Pastor</span><span class="font-semibold text-right">{{ $reg->fcc_pastor }}</span></div>
                @endif
            </div>
            @endif

            <div class="border-t border-gray-200 pt-3 flex justify-between gap-2">
                <span class="font-bold text-summit shrink-0">Registration Fee</span>
                <span class="font-extrabold text-gold text-lg text-right">
                    {{ $reg->currency === 'USD' ? '$' . number_format($reg->total_amount) . ' USD' : 'UGX ' . number_format($reg->total_amount) }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('register.step2.save', $embed ? ['embed' => 1] : []) }}">
            @csrf
            <div class="flex gap-3">
                <a href="{{ route('register.step1', $embed ? ['embed' => 1] : []) }}"
                   class="flex-1 border-2 border-gray-300 text-gray-600 font-bold py-3 rounded-xl text-center transition hover:bg-gray-50">
                    ← Edit
                </a>
                <button type="submit"
                        class="flex-grow bg-gold hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition shadow">
                    Continue to Payment →
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
