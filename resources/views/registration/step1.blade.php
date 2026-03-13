@extends('layouts.public')
@section('title', 'Step 1 – Personal Details | Renewal Summit 2026')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    @include('registration.partials.steps', ['currentStep' => 1])

    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-extrabold text-summit mb-1">Step 1: Personal Details</h2>
        <p class="text-sm text-gray-500 mb-6">Enter your personal information below.</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-5 text-sm">
                <ul class="list-disc ml-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.step1.save') }}">
            @csrf

            {{-- Full Name --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name', $reg->full_name ?? '') }}"
                       placeholder="e.g. John Mukasa"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition"
                       required>
            </div>

            {{-- Designation --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Designation <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3" id="designation-grid">
                    @php
                    $designations = [
                        'fcc_regional_leader' => ['icon' => '✓', 'label' => 'FCC Regional Leader'],
                        'senior_pastor'       => ['icon' => '✝️',  'label' => 'Senior Pastor'],
                        'church_leader'       => ['icon' => '🏩', 'label' => 'Church Leader'],
                        'corporate'           => ['icon' => '🏢', 'label' => 'Corporate / Organisation'],
                    ];
                    $currentDesig = old('designation', $reg->designation ?? 'senior_pastor');
                    @endphp
                    @foreach($designations as $val => $d)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="designation" value="{{ $val }}" class="sr-only peer desig-radio"
                               {{ $currentDesig === $val ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl px-3 py-3 text-sm font-medium text-center transition
                                    peer-checked:border-yellow-400 peer-checked:bg-yellow-50 peer-checked:text-yellow-700
                                    border-gray-200 hover:border-gray-400">
                            <div class="text-xl mb-0.5">{{ $d['icon'] }}</div>
                            <div class="font-semibold text-xs">{{ $d['label'] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
                {{-- Specify field for Church Leader --}}
                <div id="desig-specify" class="mt-3 {{ $currentDesig === 'church_leader' ? '' : 'hidden' }}">
                    <input type="text" name="designation_specify"
                           value="{{ old('designation_specify', $reg->designation_specify ?? '') }}"
                           placeholder="Please specify your role..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                </div>
            </div>

            {{-- Phone --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                <input type="tel" name="phone" value="{{ old('phone', $reg->phone ?? '') }}"
                       placeholder="e.g. 0772123456 or 256772123456"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition"
                       required>
                <p class="text-xs text-gray-400 mt-1">This will also be used to resume your registration.</p>
            </div>

            {{-- Email --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email (for QR code delivery)</label>
                <input type="email" name="email" value="{{ old('email', $reg->email ?? '') }}"
                       placeholder="e.g. john@example.com"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                <p class="text-xs text-gray-400 mt-1">Your QR code will be sent here after payment.</p>
            </div>

            {{-- Address --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Address / City</label>
                <input type="text" name="address" value="{{ old('address', $reg->address ?? '') }}"
                       placeholder="e.g. Kampala, Uganda"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
            </div>

            {{-- Country Type --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Attendee Type <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-3">
                    @php
                    $types = [
                        'local'         => ['flag' => '🇺🇬', 'label' => 'Ugandan',        'fee' => 'UGX 150,000'],
                        'africa'        => ['flag' => '🌍', 'label' => 'Rest of Africa', 'fee' => '$50 USD'],
                        'international' => ['flag' => '✈️',  'label' => 'International',  'fee' => '$100 USD'],
                    ];
                    $currentType = old('country_type', $reg->country_type ?? 'local');
                    @endphp
                    @foreach($types as $val => $t)
                    <label class="relative cursor-pointer">
                        <input type="radio" name="country_type" value="{{ $val }}" class="sr-only peer"
                               {{ $currentType === $val ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl px-3 py-3 text-center transition
                                    peer-checked:border-yellow-400 peer-checked:bg-yellow-50
                                    border-gray-200 hover:border-gray-400">
                            <div class="text-xl mb-0.5">{{ $t['flag'] }}</div>
                            <div class="font-bold text-xs text-summit">{{ $t['label'] }}</div>
                            <div class="text-xs text-green-700 font-semibold mt-0.5">{{ $t['fee'] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Nationality (always visible, label adapts) --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Country of Residence</label>
                <input type="text" name="nationality" value="{{ old('nationality', $reg->nationality ?? '') }}"
                       placeholder="e.g. Uganda, Kenya, Nigeria, USA..."
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
            </div>

            <button type="submit"
                    class="w-full bg-gold hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition text-lg shadow">
                Continue to Step 2 →
            </button>
        </form>
    </div>
</div>
@push('scripts')
<script>
    // Toggle designation_specify field
    document.querySelectorAll('.desig-radio').forEach(function(el) {
        el.addEventListener('change', function() {
            document.getElementById('desig-specify').classList.toggle('hidden', this.value !== 'church_leader');
        });
    });
</script>
@endpush
@endsection
