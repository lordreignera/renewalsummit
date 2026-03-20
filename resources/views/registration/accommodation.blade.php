@extends('layouts.public')
@section('title', 'Accommodation Booking | Renewal Summit 2026')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-lg p-5 sm:p-8">
        <h1 class="text-2xl font-extrabold text-summit">Accommodation Planning</h1>
        <p class="text-sm text-gray-500 mt-1">Select where you will stay after completing registration payment.</p>

        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3 mt-5 text-sm">
            Registration Ref: <strong>{{ $reg->reference }}</strong> | Guest: <strong>{{ $reg->full_name }}</strong>
        </div>

        <div class="flex items-center gap-2 mt-3">
            <span class="inline-flex items-center gap-2 bg-yellow-50 border border-yellow-200 text-yellow-800 text-xs font-semibold px-4 py-2 rounded-full">
                💱 Exchange rate: <strong>1 USD ≈ UGX {{ number_format(config('app.usd_ugx_rate', 3700)) }}</strong>
            </span>
        </div>

        <form method="POST" action="{{ route('register.accommodation.save', ['reference' => $reg->reference, 'token' => $reg->qr_token]) }}" class="mt-6 space-y-6" id="accommodation-form">
            @csrf

            <div>
                <label class="block text-sm font-bold text-summit mb-2">How would you like to handle accommodation?</label>
                <div class="grid sm:grid-cols-3 gap-3">
                    @php $modeOld = old('accommodation_booking_mode', $reg->accommodation_booking_mode ?? 'self_book'); @endphp
                    <label class="border rounded-xl p-3 cursor-pointer">
                        <input type="radio" name="accommodation_booking_mode" value="self_book" class="mr-2" {{ $modeOld === 'self_book' ? 'checked' : '' }}>
                        <span class="font-semibold text-sm">I will book for myself</span>
                    </label>
                    <label class="border rounded-xl p-3 cursor-pointer">
                        <input type="radio" name="accommodation_booking_mode" value="book_through_us_no_payment" class="mr-2" {{ $modeOld === 'book_through_us_no_payment' ? 'checked' : '' }}>
                        <span class="font-semibold text-sm">Book through us (pay later)</span>
                    </label>
                    <label class="border rounded-xl p-3 cursor-pointer">
                        <input type="radio" name="accommodation_booking_mode" value="book_through_us_and_pay" class="mr-2" {{ $modeOld === 'book_through_us_and_pay' ? 'checked' : '' }}>
                        <span class="font-semibold text-sm">Book through us and pay now</span>
                    </label>
                </div>
            </div>

            @if($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Self-book hotel links (shown only when self_book mode is active) --}}
            <div id="self-book-links" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-sm font-bold text-blue-800 mb-3">📋 Book directly with your chosen hotel:</p>
                <div class="space-y-2">
                    @foreach($hotels as $h)
                    <div class="flex items-center justify-between bg-white border border-blue-100 rounded-xl px-4 py-2.5 text-sm">
                        <span class="font-semibold text-summit">{{ $h->name }}</span>
                        @if($h->booking_url)
                        <a href="{{ $h->booking_url }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold text-xs border border-blue-300 rounded-lg px-3 py-1 hover:bg-blue-50 transition">
                            Book Now
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        </a>
                        @else
                        <span class="text-xs text-gray-400">Contact hotel directly</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-blue-600 mt-3">You can still indicate your preferred hotel below so we can track it.</p>
            </div>

            <div class="grid lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-summit mb-1">Hotel</label>
                    <select name="accommodation_hotel_id" id="hotel-id" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm" required>
                        <option value="">Choose hotel</option>
                        @foreach($hotels as $hotel)
                        <option value="{{ $hotel->id }}"
                                data-single-usd="{{ $hotel->single_price_usd }}"
                                data-double-usd="{{ $hotel->double_price_usd }}"
                                data-single-ugx="{{ $hotel->single_price_ugx }}"
                                data-double-ugx="{{ $hotel->double_price_ugx }}"
                                {{ (string) old('accommodation_hotel_id', $reg->accommodation_hotel_id) === (string) $hotel->id ? 'selected' : '' }}>
                            {{ $hotel->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div id="room-type-wrap">
                    <label class="block text-sm font-bold text-summit mb-1">Room Type</label>
                    @php $roomOld = old('accommodation_room_type', $reg->accommodation_room_type ?? 'single'); @endphp
                    <select name="accommodation_room_type" id="room-type" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm" required>
                        <option value="single" {{ $roomOld === 'single' ? 'selected' : '' }}>Single Room</option>
                        <option value="double" {{ $roomOld === 'double' ? 'selected' : '' }}>Double Room</option>
                    </select>
                </div>
            </div>

            <div id="nights-fee-row" class="grid lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold text-summit mb-1">Number of Nights</label>
                    <input type="number" min="1" max="14" step="1" name="accommodation_nights" id="nights" value="{{ old('accommodation_nights', $reg->accommodation_nights ?? 1) }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-summit mb-1">Payment Currency</label>
                    @php $currOld = old('accommodation_currency', $reg->accommodation_currency ?: $reg->currency); @endphp
                    <select name="accommodation_currency" id="accommodation-currency" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm">
                        <option value="USD" {{ $currOld === 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="UGX" {{ $currOld === 'UGX' ? 'selected' : '' }}>UGX</option>
                    </select>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm">
                    <div class="text-gray-500">Estimated Total</div>
                    <div id="accommodation-total" class="text-lg font-extrabold text-gold">--</div>
                    <input type="hidden" name="accommodation_fee" id="accommodation-fee" value="{{ old('accommodation_fee', $reg->accommodation_fee ?? 0) }}">
                </div>
            </div>

            <div id="pay-now-fields" class="hidden bg-green-50 border border-green-200 rounded-xl p-4 space-y-4">
                <h3 class="font-bold text-green-800">Pay Accommodation Now</h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                    @php $pmOld = old('payment_method', 'mobile_money'); @endphp
                    <div class="grid grid-cols-2 gap-3">
                        <label class="border rounded-xl p-3 cursor-pointer bg-white">
                            <input type="radio" name="payment_method" value="mobile_money" class="mr-2" {{ $pmOld === 'mobile_money' ? 'checked' : '' }}>
                            Mobile Money
                        </label>
                        <label class="border rounded-xl p-3 cursor-pointer bg-white">
                            <input type="radio" name="payment_method" value="visa" class="mr-2" {{ $pmOld === 'visa' ? 'checked' : '' }}>
                            VISA / Card
                        </label>
                    </div>
                </div>

                <div id="acc-mm-fields">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mobile Money Number</label>
                    <input type="tel" name="phone_number" value="{{ old('phone_number', $reg->phone) }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm" placeholder="e.g. 0772123456">
                    <div class="bg-amber-50 border border-amber-300 rounded-xl px-4 py-3 mt-3 text-sm text-amber-800 flex gap-3 items-start">
                        <span class="text-xl leading-none flex-shrink-0">📲</span>
                        <div>
                            <span class="font-bold">You will receive a USSD prompt</span> on your phone from
                            <strong>Swapp Payment Systems</strong>. When it appears, enter your
                            <strong>mobile money PIN</strong> to complete the payment.
                            <span class="block text-amber-700 text-xs mt-1">Do not close the prompt — it will disappear automatically once payment is confirmed.</span>
                        </div>
                    </div>
                </div>

                <div id="acc-visa-fields" class="hidden grid grid-cols-2 gap-3">
                    <input type="text" name="card_name" value="{{ old('card_name', $reg->full_name) }}" placeholder="Cardholder Name" class="border border-gray-300 rounded-xl px-4 py-3 text-sm col-span-2">
                    <input type="text" name="card_number" value="{{ old('card_number') }}" placeholder="Card Number" class="border border-gray-300 rounded-xl px-4 py-3 text-sm col-span-2">
                    <input type="text" name="card_expiry" value="{{ old('card_expiry') }}" placeholder="MM/YY" class="border border-gray-300 rounded-xl px-4 py-3 text-sm">
                    <input type="text" name="card_cvc" value="{{ old('card_cvc') }}" placeholder="CVC" class="border border-gray-300 rounded-xl px-4 py-3 text-sm">
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('register.complete', ['ref' => $reg->reference]) }}" class="border border-gray-300 text-gray-700 font-bold py-3 px-5 rounded-xl">Back</a>
                <button type="submit" class="bg-summit hover:bg-blue-900 text-white font-bold py-3 px-6 rounded-xl">Save Accommodation Plan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var hotel = document.getElementById('hotel-id');
    var room = document.getElementById('room-type');
    var nights = document.getElementById('nights');
    var currency = document.getElementById('accommodation-currency');
    var total = document.getElementById('accommodation-total');
    var fee = document.getElementById('accommodation-fee');
    var payFields = document.getElementById('pay-now-fields');
    var modeInputs = document.querySelectorAll('[name="accommodation_booking_mode"]');

    function compute() {
        var opt = hotel.options[hotel.selectedIndex];
        if (!opt || !opt.value) {
            total.textContent = '--';
            fee.value = '0';
            return;
        }

        var c = currency.value;
        var r = room.value;
        var n = Math.max(1, parseInt(nights.value || '1', 10));

        var perNight;
        if (c === 'USD') {
            perNight = r === 'double' ? parseInt(opt.dataset.doubleUsd || '0', 10) : parseInt(opt.dataset.singleUsd || '0', 10);
            total.textContent = '$' + (perNight * n).toLocaleString() + ' USD';
        } else {
            perNight = r === 'double' ? parseInt(opt.dataset.doubleUgx || '0', 10) : parseInt(opt.dataset.singleUgx || '0', 10);
            total.textContent = 'UGX ' + (perNight * n).toLocaleString();
        }

        fee.value = String(perNight * n);
    }

    var nightsFeeRow = document.getElementById('nights-fee-row');
    var selfBookLinks = document.getElementById('self-book-links');
    var roomTypeWrap = document.getElementById('room-type-wrap');

    function togglePayNow() {
        var mode = document.querySelector('[name="accommodation_booking_mode"]:checked');
        var isSelfBook = mode && mode.value === 'self_book';
        var isPayNow = mode && mode.value === 'book_through_us_and_pay';
        payFields.classList.toggle('hidden', !isPayNow);
        nightsFeeRow.classList.toggle('hidden', isSelfBook);
        selfBookLinks.classList.toggle('hidden', !isSelfBook);
        roomTypeWrap.classList.toggle('hidden', isSelfBook);
    }

    function togglePayMethod() {
        var pm = document.querySelector('[name="payment_method"]:checked');
        var visa = pm && pm.value === 'visa';
        document.getElementById('acc-mm-fields').classList.toggle('hidden', visa);
        document.getElementById('acc-visa-fields').classList.toggle('hidden', !visa);
    }

    [hotel, room, nights, currency].forEach(function (el) {
        el.addEventListener('change', compute);
        el.addEventListener('input', compute);
    });

    modeInputs.forEach(function (el) {
        el.addEventListener('change', togglePayNow);
    });

    document.querySelectorAll('[name="payment_method"]').forEach(function (el) {
        el.addEventListener('change', togglePayMethod);
    });

    compute();
    togglePayNow();
    togglePayMethod();

})();
</script>
@endpush
@endsection
