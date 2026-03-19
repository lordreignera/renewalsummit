@extends('layouts.admin')
@section('title', 'New Registration')
@section('page-title', 'Create Registration')

@section('content')
<div class="bg-white rounded-2xl shadow-sm p-6 max-w-3xl">
    @if(session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold mb-1">Please fix the following:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.registrations.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @php $currentAff = old('affiliation'); @endphp
            @php $currentDesig = old('designation'); @endphp

            <div>
                <label class="text-sm font-bold">1. Where are you attending from? <span class="text-red-500">*</span></label>
                <select name="country_type" class="w-full border rounded-xl px-3 py-2 mt-1 text-sm bg-white" required>
                    <option value="">- Select your location -</option>
                    <option value="local" {{ old('country_type') === 'local' ? 'selected' : '' }}>Ugandan Delegate</option>
                    <option value="africa" {{ old('country_type') === 'africa' ? 'selected' : '' }}>Rest of Africa Delegate</option>
                    <option value="international" {{ old('country_type') === 'international' ? 'selected' : '' }}>International Delegate</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-bold">2. Are you an FCC member? <span class="text-red-500">*</span></label>
                <select name="affiliation" id="affiliation" required class="w-full border rounded-xl px-3 py-2 mt-1 text-sm bg-white">
                    <option value="">- Select -</option>
                    <option value="fcc" {{ $currentAff === 'fcc' ? 'selected' : '' }}>Yes - I am an FCC member</option>
                    <option value="other" {{ $currentAff === 'other' ? 'selected' : '' }}>No - I am not an FCC member</option>
                </select>
            </div>

            <div id="fcc-fields" class="md:col-span-2 {{ $currentAff === 'fcc' ? '' : 'hidden' }} bg-yellow-50 border border-yellow-200 rounded-xl p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-bold">Region</label>
                        <input type="text" name="fcc_region" value="{{ old('fcc_region') }}"
                               placeholder="e.g. East Africa Region"
                               class="w-full border rounded-xl px-3 py-2 mt-1 text-sm bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-bold">FCC Regional Leader</label>
                        <input type="text" name="fcc_regional_leader" value="{{ old('fcc_regional_leader') }}"
                               placeholder="Name of your FCC regional leader"
                               class="w-full border rounded-xl px-3 py-2 mt-1 text-sm bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-bold">Church Name</label>
                        <input type="text" name="fcc_church" value="{{ old('fcc_church') }}"
                               placeholder="e.g. Ggaba Community Church"
                               class="w-full border rounded-xl px-3 py-2 mt-1 text-sm bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-bold">Local Pastor Name</label>
                        <input type="text" name="fcc_pastor" value="{{ old('fcc_pastor') }}"
                               placeholder="e.g. Pastor James Katarikawe"
                               class="w-full border rounded-xl px-3 py-2 mt-1 text-sm bg-white">
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="text-sm font-bold">Full name</label>
                <input name="full_name" value="{{ old('full_name') }}" required
                       class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">
            </div>

            <div>
                <label class="text-sm font-bold">Designation</label>
                <select name="designation" id="designation"
                        required
                        class="w-full border rounded-xl px-3 py-2 mt-1 text-sm bg-white">
                    <option value="">- Select designation -</option>
                    <option value="fcc_regional_leader" {{ $currentDesig === 'fcc_regional_leader' ? 'selected' : '' }}>FCC Regional Leader</option>
                    <option value="senior_pastor" {{ $currentDesig === 'senior_pastor' ? 'selected' : '' }}>Senior Pastor</option>
                    <option value="church_leader" {{ $currentDesig === 'church_leader' ? 'selected' : '' }}>Church Leader</option>
                    <option value="corporate" {{ $currentDesig === 'corporate' ? 'selected' : '' }}>Corporate / Organisation</option>
                </select>
            </div>

            <div id="designation-specify-wrap" class="{{ old('designation') === 'church_leader' ? '' : 'hidden' }}">
                <label class="text-sm font-bold">Specify church role</label>
                <input name="designation_specify" value="{{ old('designation_specify') }}"
                       placeholder="Please specify..."
                       class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">
            </div>

            <div>
                <label class="text-sm font-bold">Phone</label>
                <input name="phone" value="{{ old('phone') }}" required
                       class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">
            </div>

            <div>
                <label class="text-sm font-bold">Email</label>
                <input name="email" value="{{ old('email') }}" type="email"
                       class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="text-sm font-bold">Address</label>
                <input name="address" value="{{ old('address') }}"
                       class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">
            </div>

            <div>
                <label class="text-sm font-bold">Nationality</label>
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
                <select name="nationality" class="w-full border rounded-xl px-3 py-2 mt-1 text-sm bg-white">
                    <option value="">- Select country -</option>
                    @foreach($countries as $country)
                        <option value="{{ $country }}" {{ old('nationality') === $country ? 'selected' : '' }}>{{ $country }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <h3 class="font-bold mt-4">Emergency & Medical</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
                    <div>
                        <label class="text-sm">Emergency contact name</label>
                        <input name="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                               class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">
                    </div>
                    <div>
                        <label class="text-sm">Emergency contact phone</label>
                        <input name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}"
                               class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm">Medical conditions (if any)</label>
                        <textarea name="medical_conditions" rows="3" class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">{{ old('medical_conditions') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm">Food allergies (if any)</label>
                        <textarea name="allergies" rows="2" class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">{{ old('allergies') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm">Mobility / accessibility needs</label>
                        <textarea name="mobility_needs" rows="2" class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">{{ old('mobility_needs') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm">Other special needs</label>
                        <textarea name="special_needs" rows="2" class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">{{ old('special_needs') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <h3 class="font-bold mt-4">Accommodation</h3>
                <p class="text-xs text-gray-500 mt-1">Accommodation details are captured now, but payment is handled separately after registration fee payment.</p>
                <div class="flex items-center gap-3 mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="accommodation_required" value="1" class="mr-2">
                        Require accommodation
                    </label>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                    <div>
                        <label class="text-sm">Accommodation choice</label>
                        <select name="accommodation_choice" class="w-full border rounded-xl px-3 py-2 mt-1 text-sm bg-white">
                            <option value="">- Select hotel -</option>
                            <option value="Speke Resort Munyonyo" {{ old('accommodation_choice') === 'Speke Resort Munyonyo' ? 'selected' : '' }}>Speke Resort Munyonyo</option>
                            <option value="Protea Hotel by Marriott" {{ old('accommodation_choice') === 'Protea Hotel by Marriott' ? 'selected' : '' }}>Protea Hotel by Marriott</option>
                            <option value="San Jose Hotel Kampala" {{ old('accommodation_choice') === 'San Jose Hotel Kampala' ? 'selected' : '' }}>San Jose Hotel Kampala</option>
                            <option value="Hotel Africana" {{ old('accommodation_choice') === 'Hotel Africana' ? 'selected' : '' }}>Hotel Africana</option>
                            <option value="St Mbaga Hotel" {{ old('accommodation_choice') === 'St Mbaga Hotel' ? 'selected' : '' }}>St Mbaga Hotel</option>
                            <option value="Other" {{ old('accommodation_choice') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Accommodation fee (UGX)</label>
                        <input name="accommodation_fee" value="{{ old('accommodation_fee') }}" type="number" class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="inline-flex items-center mt-3">
                    <input type="checkbox" name="sms_opt_in" value="1" class="mr-2">
                    Send SMS notifications to this attendee
                </label>
            </div>

            <div class="md:col-span-2">
                <label class="text-sm">Admin notes</label>
                <textarea name="admin_notes" rows="3" class="w-full border rounded-xl px-3 py-2 mt-1 text-sm">{{ old('admin_notes') }}</textarea>
            </div>

        </div>

        <div class="mt-6 flex gap-3">
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl transition">Create Registration</button>
            <a href="{{ route('admin.registrations.index') }}" class="px-4 py-2 border rounded-xl text-sm">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const designation = document.getElementById('designation');
        const specifyWrap = document.getElementById('designation-specify-wrap');
        const affiliation = document.getElementById('affiliation');
        const fccFields = document.getElementById('fcc-fields');

        if (!designation || !specifyWrap) {
            return;
        }

        function syncDesignationSpecify() {
            if (designation.value === 'church_leader') {
                specifyWrap.classList.remove('hidden');
                return;
            }

            specifyWrap.classList.add('hidden');
        }

        designation.addEventListener('change', syncDesignationSpecify);
        syncDesignationSpecify();

        if (affiliation && fccFields) {
            function syncFccFields() {
                if (affiliation.value === 'fcc') {
                    fccFields.classList.remove('hidden');
                    return;
                }

                fccFields.classList.add('hidden');
            }

            affiliation.addEventListener('change', syncFccFields);
            syncFccFields();
        }
    })();
</script>
@endpush
