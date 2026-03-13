@extends('layouts.public')
@section('title', 'Step 2 – Church Affiliation | Renewal Summit 2026')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    @include('registration.partials.steps', ['currentStep' => 2])

    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-extrabold text-summit mb-1">Step 2: Church Affiliation</h2>
        <p class="text-sm text-gray-500 mb-6">Tell us about your church involvement.</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-5 text-sm">
                <ul class="list-disc ml-4 space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.step2.save') }}" id="affiliation-form">
            @csrf

            {{-- Affiliation selector --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Are you part of FCC (Fellowship of Christian Churches)? <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="affiliation" value="fcc" id="aff-fcc"
                               class="sr-only peer"
                               {{ old('affiliation', $reg->affiliation ?? '') === 'fcc' ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl px-4 py-4 text-sm font-medium text-center transition
                                    peer-checked:border-yellow-400 peer-checked:bg-yellow-50 peer-checked:text-yellow-700
                                    border-gray-200 hover:border-gray-400">
                            <div class="text-2xl mb-1">✝️</div>
                            <div class="font-bold">Yes, I'm FCC</div>
                            <div class="text-xs text-gray-400 mt-1">Fellowship of Christian Churches</div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="affiliation" value="other" id="aff-other"
                               class="sr-only peer"
                               {{ old('affiliation', $reg->affiliation ?? 'other') === 'other' ? 'checked' : '' }}>
                        <div class="border-2 rounded-xl px-4 py-4 text-sm font-medium text-center transition
                                    peer-checked:border-yellow-400 peer-checked:bg-yellow-50 peer-checked:text-yellow-700
                                    border-gray-200 hover:border-gray-400">
                            <div class="text-2xl mb-1">🌍</div>
                            <div class="font-bold">Other / Guest</div>
                            <div class="text-xs text-gray-400 mt-1">Not part of FCC</div>
                        </div>
                    </label>
                </div>
            </div>

            {{-- FCC-only fields --}}
            <div id="fcc-fields" class="{{ old('affiliation', $reg->affiliation ?? 'other') === 'fcc' ? '' : 'hidden' }}
                                         bg-yellow-50 border border-yellow-200 rounded-xl p-5 space-y-4 mb-6">

                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-gold text-white text-xs font-bold px-2 py-0.5 rounded">FCC</span>
                    <span class="text-sm text-yellow-700 font-medium">FCC members: registration fee applies · accommodation step is skipped</span>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        FCC Region <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="fcc_region" value="{{ old('fcc_region', $reg->fcc_region ?? '') }}"
                           placeholder="e.g. East Africa Region"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        FCC Regional Leader <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="fcc_regional_leader" value="{{ old('fcc_regional_leader', $reg->fcc_regional_leader ?? '') }}"
                           placeholder="Name of your FCC regional leader"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Your Church <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="fcc_church" value="{{ old('fcc_church', $reg->fcc_church ?? '') }}"
                           placeholder="e.g. Gaba Community Church"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pastor's Name</label>
                    <input type="text" name="fcc_pastor" value="{{ old('fcc_pastor', $reg->fcc_pastor ?? '') }}"
                           placeholder="e.g. Pastor James Katarikawe"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('register.step1') }}"
                   class="flex-1 border-2 border-gray-300 text-gray-600 font-bold py-3 rounded-xl text-center transition hover:bg-gray-50">
                    ← Back
                </a>
                <button type="submit"
                        class="flex-2 flex-grow bg-gold hover:bg-yellow-600 text-white font-bold py-3 rounded-xl transition shadow">
                    Continue to Step 3 →
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('[name="affiliation"]').forEach(function(el) {
        el.addEventListener('change', function() {
            document.getElementById('fcc-fields').classList.toggle('hidden', this.value !== 'fcc');
        });
    });
</script>
@endpush
@endsection
