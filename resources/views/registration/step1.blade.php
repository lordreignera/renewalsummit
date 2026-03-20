@extends('layouts.public')
@section('title', 'Step 1 – Personal Details | Renewal Summit 2026')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    @include('registration.partials.steps', ['currentStep' => 1])

    <div class="bg-white rounded-2xl shadow-lg p-5 sm:p-8">
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

            {{-- Country Type – FIRST question --}}
            @php $currentType = old('country_type', $reg->country_type ?? ''); @endphp
            <div class="mb-7">
                <label for="country_type" class="block text-sm font-semibold text-gray-700 mb-1">
                    1. Where are you attending from? <span class="text-red-500">*</span>
                </label>
                <select name="country_type" id="country_type" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition">
                    <option value="" disabled {{ $currentType === '' ? 'selected' : '' }}>— Select your location —</option>
                    <option value="local"         {{ $currentType === 'local'         ? 'selected' : '' }}>🇺🇬 Ugandan Delegate </option>
                    <option value="africa"        {{ $currentType === 'africa'        ? 'selected' : '' }}>🌍 Rest of Africa Delegate </option>
                    <option value="international" {{ $currentType === 'international' ? 'selected' : '' }}>✈️ International Delegate </option>
                </select>
                {{-- Live fee badge --}}
                <div id="fee-badge" class="mt-2 hidden">
                    <span class="inline-block bg-green-50 border border-green-200 text-green-700 text-sm font-semibold rounded-lg px-4 py-2">
                        Registration fee: <span id="fee-label"></span>
                    </span>
                </div>
            </div>

            {{-- FCC Membership – SECOND question --}}
            @php $currentAff = old('affiliation', $reg->affiliation ?? ''); @endphp
            <div class="mb-7">
                <label for="affiliation" class="block text-sm font-semibold text-gray-700 mb-1">
                    2. Are you an FCC (Fellowship of Christian Churches) member? <span class="text-red-500">*</span>
                </label>
                <select name="affiliation" id="affiliation" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition">
                    <option value="" disabled {{ $currentAff === '' ? 'selected' : '' }}>— Select —</option>
                    <option value="fcc"   {{ $currentAff === 'fcc'   ? 'selected' : '' }}>Yes — I am an FCC member</option>
                    <option value="other" {{ $currentAff === 'other' ? 'selected' : '' }}>No — I am not an FCC member</option>
                </select>

                {{-- FCC fields (shown when Yes is selected) --}}
                <div id="fcc-fields" class="mt-4 {{ $currentAff === 'fcc' ? '' : 'hidden' }}
                                              bg-yellow-50 border border-yellow-200 rounded-xl p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Region <span class="text-red-500">*</span></label>
                        <input type="text" name="fcc_region" value="{{ old('fcc_region', $reg->fcc_region ?? '') }}"
                               placeholder="e.g. East Africa Region"
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">FCC Regional Leader <span class="text-red-500">*</span></label>
                        <input type="text" name="fcc_regional_leader" value="{{ old('fcc_regional_leader', $reg->fcc_regional_leader ?? '') }}"
                               placeholder="Name of your FCC regional leader"
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Church Name <span class="text-red-500">*</span></label>
                        <input type="text" name="fcc_church" value="{{ old('fcc_church', $reg->fcc_church ?? '') }}"
                               placeholder="e.g. Gaba Community Church"
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Local Pastor Name</label>
                        <input type="text" name="fcc_pastor" value="{{ old('fcc_pastor', $reg->fcc_pastor ?? '') }}"
                               placeholder="e.g. Pastor James Katarikawe"
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                    </div>
                </div>
            </div>

            <hr class="border-gray-100 mb-6">

            {{-- Full Name --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name', $reg->full_name ?? '') }}"
                       placeholder="e.g. John Mukasa"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition"
                       required>
            </div>

            {{-- Designation --}}
            @php $currentDesig = old('designation', $reg->designation ?? ''); @endphp
            <div class="mb-5">
                <label for="designation" class="block text-sm font-semibold text-gray-700 mb-1">
                    Designation <span class="text-red-500">*</span>
                </label>
                <select name="designation" id="designation" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm bg-white focus:ring-2 focus:ring-yellow-400 outline-none transition">
                    <option value="" disabled {{ $currentDesig === '' ? 'selected' : '' }}>— Select your designation —</option>
                    <option value="fcc_regional_leader" {{ $currentDesig === 'fcc_regional_leader' ? 'selected' : '' }}>FCC Regional Leader</option>
                    <option value="senior_pastor"       {{ $currentDesig === 'senior_pastor'       ? 'selected' : '' }}>Senior Pastor</option>
                    <option value="church_leader"       {{ $currentDesig === 'church_leader'       ? 'selected' : '' }}>Church Leader</option>
                    <option value="corporate"           {{ $currentDesig === 'corporate'           ? 'selected' : '' }}>Corporate / Organisation</option>
                </select>
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

            {{-- Country of Residence – custom searchable dropdown --}}
            @php $savedCountry = old('nationality', $reg->nationality ?? ''); @endphp
            <div class="mb-6" id="country-wrapper">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Country of Residence</label>

                {{-- Trigger button --}}
                <button type="button" id="country-trigger"
                        class="w-full flex items-center justify-between border border-gray-300 rounded-xl px-4 py-3 text-sm bg-white
                               hover:border-yellow-400 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 outline-none transition text-left">
                    <span id="country-display" class="{{ $savedCountry ? 'text-gray-800' : 'text-gray-400' }}">
                        {{ $savedCountry ?: 'Select your country...' }}
                    </span>
                    <svg id="country-chevron" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Dropdown panel --}}
                <div id="country-panel"
                     class="hidden mt-1 border border-gray-200 rounded-xl shadow-lg bg-white overflow-hidden z-50">

                    {{-- Search inside panel --}}
                    <div class="p-2 border-b border-gray-100">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                            </svg>
                            <input type="text" id="country-search"
                                   placeholder="Search country..."
                                   autocomplete="off"
                                   class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-400 outline-none transition">
                        </div>
                    </div>

                    {{-- Country list --}}
                    <ul id="country-list" class="max-h-52 overflow-y-auto py-1" role="listbox">
                        @php
                        $countries = [
                            'Uganda','Afghanistan','Albania','Algeria','Andorra','Angola','Antigua and Barbuda',
                            'Argentina','Armenia','Australia','Austria','Azerbaijan','Bahamas','Bahrain',
                            'Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bhutan','Bolivia',
                            'Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria','Burkina Faso',
                            'Burundi','Cabo Verde','Cambodia','Cameroon','Canada','Central African Republic',
                            'Chad','Chile','China','Colombia','Comoros','Congo (Brazzaville)',
                            'Congo (Kinshasa)','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic',
                            'Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt',
                            'El Salvador','Equatorial Guinea','Eritrea','Estonia','Eswatini','Ethiopia',
                            'Fiji','Finland','France','Gabon','Gambia','Georgia','Germany','Ghana',
                            'Greece','Grenada','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti',
                            'Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland',
                            'Israel','Italy','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati',
                            'Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya',
                            'Liechtenstein','Lithuania','Luxembourg','Madagascar','Malawi','Malaysia',
                            'Maldives','Mali','Malta','Marshall Islands','Mauritania','Mauritius','Mexico',
                            'Micronesia','Moldova','Monaco','Mongolia','Montenegro','Morocco','Mozambique',
                            'Myanmar','Namibia','Nauru','Nepal','Netherlands','New Zealand','Nicaragua',
                            'Niger','Nigeria','North Korea','North Macedonia','Norway','Oman','Pakistan',
                            'Palau','Palestine','Panama','Papua New Guinea','Paraguay','Peru','Philippines',
                            'Poland','Portugal','Qatar','Romania','Russia','Rwanda',
                            'Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines',
                            'Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal',
                            'Serbia','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia',
                            'Solomon Islands','Somalia','South Africa','South Korea','South Sudan',
                            'Spain','Sri Lanka','Sudan','Suriname','Sweden','Switzerland','Syria',
                            'Taiwan','Tajikistan','Tanzania','Thailand','Timor-Leste','Togo','Tonga',
                            'Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Tuvalu','UAE',
                            'United Kingdom','United States','Uruguay','Uzbekistan','Vanuatu',
                            'Vatican City','Venezuela','Vietnam','Yemen','Zambia','Zimbabwe',
                        ];
                        @endphp
                        @foreach($countries as $c)
                        <li data-value="{{ $c }}"
                            class="px-4 py-2 text-sm cursor-pointer transition
                                   {{ $savedCountry === $c ? 'bg-yellow-50 text-yellow-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}"
                            role="option">
                            {{ $c }}
                        </li>
                        @endforeach
                        <li id="country-no-results" class="hidden px-4 py-3 text-sm text-gray-400 text-center italic">No countries found</li>
                    </ul>
                </div>

                {{-- Hidden input for form submission --}}
                <input type="hidden" name="nationality" id="nationality-hidden" value="{{ $savedCountry }}">
            </div>

            <hr class="border-gray-100 my-6">

            {{-- Emergency & Medical --}}
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Emergency &amp; Medical <span class="font-normal text-gray-400 uppercase tracking-normal text-xs">(optional)</span></h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Emergency contact name</label>
                    <input type="text" name="emergency_contact_name"
                           value="{{ old('emergency_contact_name', $reg->emergency_contact_name ?? '') }}"
                           placeholder="e.g. Mary Mukasa"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Emergency contact phone</label>
                    <input type="tel" name="emergency_contact_phone"
                           value="{{ old('emergency_contact_phone', $reg->emergency_contact_phone ?? '') }}"
                           placeholder="e.g. 0772000000"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Medical conditions <span class="font-normal text-gray-400">(if any)</span></label>
                <textarea name="medical_conditions" rows="2"
                          placeholder="e.g. Diabetes, high blood pressure…"
                          class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition resize-none">{{ old('medical_conditions', $reg->medical_conditions ?? '') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Food allergies <span class="font-normal text-gray-400">(if any)</span></label>
                <textarea name="allergies" rows="2"
                          placeholder="e.g. Peanuts, gluten…"
                          class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition resize-none">{{ old('allergies', $reg->allergies ?? '') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Mobility / accessibility needs</label>
                <textarea name="mobility_needs" rows="2"
                          placeholder="e.g. Wheelchair access required…"
                          class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition resize-none">{{ old('mobility_needs', $reg->mobility_needs ?? '') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Other special needs</label>
                <textarea name="special_needs" rows="2"
                          placeholder="Any other requirements we should know about…"
                          class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 outline-none transition resize-none">{{ old('special_needs', $reg->special_needs ?? '') }}</textarea>
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
    // Live fee badge for country type dropdown
    (function () {
        const fees = {
            'local':         '🇺🇬 UGX 150,000',
            'africa':        '🌍 $50 USD',
            'international': '✈️ $100 USD',
        };
        const sel   = document.getElementById('country_type');
        const badge = document.getElementById('fee-badge');
        const label = document.getElementById('fee-label');

        function updateFee() {
            const val = sel.value;
            if (fees[val]) {
                label.textContent = fees[val];
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }

        sel.addEventListener('change', updateFee);
        updateFee(); // run on page load for pre-selected values
    })();

    // FCC membership toggle
    document.getElementById('affiliation').addEventListener('change', function () {
        document.getElementById('fcc-fields').classList.toggle('hidden', this.value !== 'fcc');
    });

    // Toggle designation_specify field for Church Leader
    document.getElementById('designation').addEventListener('change', function () {
        document.getElementById('desig-specify').classList.toggle('hidden', this.value !== 'church_leader');
    });

    // Country of residence – custom dropdown
    (function () {
        const trigger  = document.getElementById('country-trigger');
        const panel    = document.getElementById('country-panel');
        const search   = document.getElementById('country-search');
        const list     = document.getElementById('country-list');
        const hidden   = document.getElementById('nationality-hidden');
        const display  = document.getElementById('country-display');
        const chevron  = document.getElementById('country-chevron');
        const noRes    = document.getElementById('country-no-results');
        const items    = Array.from(list.querySelectorAll('li[data-value]'));

        function openPanel() {
            panel.classList.remove('hidden');
            chevron.style.transform = 'rotate(180deg)';
            search.value = '';
            items.forEach(i => i.hidden = false);
            noRes.classList.add('hidden');
            search.focus();
            // Scroll selected item into view
            const sel = list.querySelector('.bg-yellow-50');
            if (sel) sel.scrollIntoView({ block: 'nearest' });
        }

        function closePanel() {
            panel.classList.add('hidden');
            chevron.style.transform = '';
        }

        function selectCountry(value, label) {
            hidden.value = value;
            display.textContent = label;
            display.classList.remove('text-gray-400');
            display.classList.add('text-gray-800');
            // Highlight selected row
            items.forEach(function (i) {
                const isSel = i.dataset.value === value;
                i.className = 'px-4 py-2 text-sm cursor-pointer transition ' +
                    (isSel ? 'bg-yellow-50 text-yellow-700 font-semibold' : 'text-gray-700 hover:bg-gray-50');
            });
            closePanel();
        }

        // Toggle open/close
        trigger.addEventListener('click', function () {
            panel.classList.contains('hidden') ? openPanel() : closePanel();
        });

        // Close when clicking outside
        document.addEventListener('click', function (e) {
            if (!document.getElementById('country-wrapper').contains(e.target)) closePanel();
        });

        // Live search filter
        search.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            let visible = 0;
            items.forEach(function (i) {
                const match = !q || i.dataset.value.toLowerCase().includes(q);
                i.hidden = !match;
                if (match) visible++;
            });
            noRes.classList.toggle('hidden', visible > 0);
        });

        // Select item on click
        items.forEach(function (i) {
            i.addEventListener('click', function () {
                selectCountry(this.dataset.value, this.dataset.value);
            });
        });

        // Keyboard navigation
        search.addEventListener('keydown', function (e) {
            const visible = items.filter(i => !i.hidden);
            if (!visible.length) return;
            if (e.key === 'ArrowDown') { visible[0].focus(); e.preventDefault(); }
            if (e.key === 'Escape')    { closePanel(); trigger.focus(); }
            if (e.key === 'Enter' && visible.length === 1) {
                selectCountry(visible[0].dataset.value, visible[0].dataset.value);
                e.preventDefault();
            }
        });
        items.forEach(function (i, idx) {
            i.setAttribute('tabindex', '-1');
            i.addEventListener('keydown', function (e) {
                const visible = items.filter(v => !v.hidden);
                const pos = visible.indexOf(i);
                if (e.key === 'ArrowDown' && pos < visible.length - 1) { visible[pos+1].focus(); e.preventDefault(); }
                if (e.key === 'ArrowUp')   { pos > 0 ? visible[pos-1].focus() : search.focus(); e.preventDefault(); }
                if (e.key === 'Enter')     { selectCountry(i.dataset.value, i.dataset.value); e.preventDefault(); }
                if (e.key === 'Escape')    { closePanel(); trigger.focus(); }
            });
        });
    })();
</script>
@endpush
@endsection
