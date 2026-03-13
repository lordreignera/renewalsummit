@extends('layouts.public')
@section('title', 'Renewal Summit 2026 – Healthy Church')

@section('content')

{{-- ── HERO ──────────────────────────────────────────────────────── --}}
<section class="relative bg-summit text-white overflow-hidden"
         style="min-height:92vh; background-image: linear-gradient(to bottom right, #1a1a2e 60%, #16213e);">

    {{-- Decorative gold bar --}}
    <div class="absolute inset-y-0 right-0 w-1/3 opacity-5"
         style="background: radial-gradient(circle, #D4A017 0%, transparent 70%)"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col-reverse lg:flex-row items-center justify-between
                py-20 gap-12 relative z-10">

        {{-- Text --}}
        <div class="lg:w-1/2 text-center lg:text-left">
            <p class="uppercase tracking-widest text-yellow-400 text-sm font-semibold mb-3">
                International Conference
            </p>
            <h1 class="text-5xl lg:text-7xl font-extrabold leading-tight mb-4">
                RENEWAL<br>
                <span class="gold">SUMMIT</span>
                <span class="text-white">2</span><span class="text-yellow-400">0</span><span class="text-white">26</span>
            </h1>

            <div class="flex items-center gap-3 justify-center lg:justify-start mb-6">
                <span class="bg-gold text-white font-bold px-3 py-1 text-sm rounded">THEME</span>
                <span class="text-xl font-bold text-white">Healthy Church</span>
            </div>

            <p class="text-gray-300 text-base leading-relaxed mb-8 max-w-lg mx-auto lg:mx-0">
                A global gathering for pastors and leaders to examine the spiritual, relational,
                and missional markers of a healthy church. <strong class="text-white">1,500 leaders
                from 27 nations.</strong>
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                <a href="{{ route('register.start') }}"
                   class="bg-gold hover:bg-yellow-600 text-white font-bold px-8 py-4 rounded-xl text-lg
                          transition shadow-xl inline-block text-center">
                    📝 Register Now
                </a>
                <a href="{{ route('donate') }}"
                   class="border-2 border-yellow-400 text-yellow-400 hover:bg-yellow-400 hover:text-summit
                          font-bold px-8 py-4 rounded-xl text-lg transition inline-block text-center">
                    🙏 Donate
                </a>
            </div>

            {{-- Date & Venue badges --}}
            <div class="flex flex-wrap gap-4 justify-center lg:justify-start mt-8">
                <div class="flex items-center gap-2 bg-white/10 rounded-lg px-4 py-2 text-sm">
                    📅 <span class="font-semibold">August 17–27, 2026</span>
                </div>
                <div class="flex items-center gap-2 bg-white/10 rounded-lg px-4 py-2 text-sm">
                    📍 <span class="font-semibold">Ggaba Community Church, Uganda</span>
                </div>
            </div>
        </div>

        {{-- Hero image area --}}
        <div class="lg:w-1/2 flex justify-center">
            <div class="relative">
                <div class="w-80 h-96 lg:w-96 lg:h-[480px] rounded-2xl overflow-hidden shadow-2xl border-4 border-yellow-400/40">
                    <img src="{{ asset('images/together.jpg') }}" alt="Renewal Summit 2026"
                         class="w-full h-full object-cover">
                </div>
                {{-- Save the date badge --}}
                <div class="absolute -top-4 -right-4 bg-gold text-white rounded-full w-20 h-20 flex flex-col
                            items-center justify-center text-center shadow-xl font-bold text-xs">
                    SAVE THE<br>DATE
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll chevron --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 animate-bounce text-gold text-2xl">⌵</div>
</section>

{{-- ── COUNTDOWN ────────────────────────────────────────────────── --}}
<section class="bg-summit py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="uppercase tracking-widest text-yellow-400 text-xs font-bold mb-2">Event Starts In</p>
        <h2 class="text-white font-extrabold text-2xl mb-10">August 17, 2026 · Ggaba Community Church, Uganda</h2>
        <div class="flex flex-wrap justify-center gap-4" id="countdown">
            @foreach(['weeks','days','hours','minutes','seconds'] as $unit)
            <div class="flex flex-col items-center gap-3">
                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl bg-white/10 border border-white/20 shadow-xl
                            flex items-center justify-center">
                    <span class="text-4xl sm:text-5xl font-extrabold text-white tabular-nums"
                          id="cd-{{ $unit }}">00</span>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-yellow-400">{{ $unit }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── STATS BAR ─────────────────────────────────────────────────── --}}
<div class="bg-gold text-white" id="stats-bar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-wrap justify-center gap-10">
        <div class="flex flex-col items-center gap-3">
            <div class="w-32 h-32 rounded-full border-4 border-white/40 bg-white/10 flex items-center justify-center shadow-lg">
                <span class="text-4xl font-extrabold" data-countup="1500" data-suffix="+">0</span>
            </div>
            <div class="text-sm font-semibold uppercase tracking-widest opacity-90">Leaders</div>
        </div>
        <div class="flex flex-col items-center gap-3">
            <div class="w-32 h-32 rounded-full border-4 border-white/40 bg-white/10 flex items-center justify-center shadow-lg">
                <span class="text-4xl font-extrabold" data-countup="27">0</span>
            </div>
            <div class="text-sm font-semibold uppercase tracking-widest opacity-90">Nations</div>
        </div>
        <div class="flex flex-col items-center gap-3">
            <div class="w-32 h-32 rounded-full border-4 border-white/40 bg-white/10 flex items-center justify-center shadow-lg">
                <span class="text-4xl font-extrabold" data-countup="5">0</span>
            </div>
            <div class="text-sm font-semibold uppercase tracking-widest opacity-90">Days</div>
        </div>
    </div>
</div>

{{-- ── ABOUT ─────────────────────────────────────────────────────── --}}
<section id="about" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="bg-gold/10 text-yellow-700 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full">About</span>
            <h2 class="text-4xl font-extrabold text-summit mt-4">About Renewal Summit 2026</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="space-y-5 text-gray-700 leading-relaxed">
                <p class="text-lg">
                    The <strong>Renewal Summit International Conference 2026</strong> is a premier gathering
                    designed to bring together pastors, ministry leaders, and church workers from across Africa
                    and the globe.
                </p>
                <p>
                    Under the theme <strong>"Healthy Church"</strong>, delegates will examine and engage
                    the spiritual, relational, and missional markers that define a thriving, God-centred church
                    in the 21st century.
                </p>
                <p>
                    Hosted at <strong>Ggaba Community Church (GCC), Uganda</strong>, this 10-day conference
                    will feature powerful keynote messages, workshops, worship, and networking opportunities.
                </p>
                <a href="{{ route('register.start') }}"
                   class="inline-block bg-summit hover:bg-blue-900 text-white font-bold px-6 py-3 rounded-lg transition mt-4">
                    Register to Attend →
                </a>
            </div>
            <div class="relative rounded-2xl overflow-hidden shadow-xl">
                <img src="{{ asset('images/praise1.jpg') }}" alt="Summit worship"
                     class="w-full h-80 object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-summit/70 to-transparent flex flex-col
                            justify-end p-6">
                    <p class="text-white font-extrabold text-xl">"A Church That Thrives"</p>
                    <p class="text-yellow-300 text-sm mt-1">Worship · Prayer · Word · Community</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── GALLERY ───────────────────────────────────────────────────── --}}
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="bg-gold/10 text-yellow-700 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full">Gallery</span>
            <h2 class="text-3xl font-extrabold text-summit mt-3">A Taste of Renewal Summit</h2>
            <p class="text-gray-500 mt-2 text-sm">Moments from previous summits — worship, community and transformation.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div class="relative rounded-2xl overflow-hidden shadow-md group h-64">
                <img src="{{ asset('images/performance1.jpg') }}" alt="Summit performance"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-summit/20 group-hover:bg-summit/10 transition"></div>
            </div>
            <div class="relative rounded-2xl overflow-hidden shadow-md group h-80">
                <img src="{{ asset('images/performance2.jpg') }}" alt="Summit worship"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-summit/20 group-hover:bg-summit/10 transition"></div>
            </div>
            <div class="relative rounded-2xl overflow-hidden shadow-md group h-64">
                <img src="{{ asset('images/performance3.jpg') }}" alt="Summit moment"
                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-summit/20 group-hover:bg-summit/10 transition"></div>
            </div>
        </div>
    </div>
</section>

{{-- ── SCHEDULE ──────────────────────────────────────────────────── --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-extrabold text-summit">Programme Schedule</h2>
            <p class="text-gray-500 mt-2">August 17–27, 2026</p>
        </div>
        <div class="space-y-4">
            @foreach([
                ['Mon Aug 17', 'Arrival & Registration',      'Check-in, welcome reception and opening worship'],
                ['Tue Aug 18', 'Spiritual Markers',           'Morning sessions, workshops, evening celebration'],
                ['Wed Aug 19', 'Relational Markers',          'Seminars, breakout sessions, networking dinner'],
                ['Thu Aug 20', 'Missional Markers',           'Keynotes, outreach activation, prayer sessions'],
                ['Fri Aug 21', 'Commissioning & Closing',     'Final session, commissioning service, departure'],
            ] as [$day, $title, $desc])
                <div class="flex gap-4 bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="min-w-[100px] text-center">
                        <div class="bg-summit text-white text-xs font-bold px-2 py-1 rounded">{{ $day }}</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-summit">{{ $title }}</h4>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── SPEAKERS PLACEHOLDER ──────────────────────────────────────── --}}
<section id="speakers" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-extrabold text-summit mb-4">Featured Speakers</h2>
        <p class="text-gray-500 mb-12">International speakers and ministry leaders. Speaker lineup coming soon.</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @for($i = 1; $i <= 4; $i++)
                <div class="bg-gray-50 rounded-xl p-6 text-center hover:shadow-md transition">
                    <div class="w-20 h-20 bg-gray-200 rounded-full mx-auto mb-3 flex items-center justify-center text-3xl">👤</div>
                    <div class="text-sm font-bold text-summit">Speaker {{ $i }}</div>
                    <div class="text-xs text-gray-400 mt-1">To Be Announced</div>
                </div>
            @endfor
        </div>
    </div>
</section>

{{-- ── ACCOMMODATION ────────────────────────────────────────────── --}}
<section class="py-16 bg-gray-50" id="accommodation">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <span class="text-gold font-bold uppercase tracking-widest text-xs">Where To Stay</span>
            <h2 class="text-3xl font-extrabold text-summit mt-2">Recommended Accommodation</h2>
            <p class="text-gray-500 mt-3 max-w-2xl mx-auto">
                The summit is held at Gaba Community Church, Kampala. The hotels below are close to the venue —
                please book directly with your preferred hotel.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            @php
            $hotels = [
                [
                    'name'    => 'Speke Resort Munyonyo',
                    'desc'    => 'Luxury lakeside resort on the shores of Lake Victoria. ~5 km from venue.',
                    'price'   => 'From UGX 350,000 / night',
                    'icon'    => '🏨',
                    'url'     => 'https://www.spekeresort.com',
                ],
                [
                    'name'    => 'Protea Hotel by Marriott',
                    'desc'    => 'International-standard hotel in Kampala city. ~8 km from venue.',
                    'price'   => 'From UGX 280,000 / night',
                    'icon'    => '🏩',
                    'url'     => 'https://www.marriott.com/en-us/hotels/enbbr-protea-hotel-kampala/',
                ],
                [
                    'name'    => 'San Jose Hotel Kampala',
                    'desc'    => 'Comfortable mid-range hotel with conference facilities. ~6 km from venue.',
                    'price'   => 'From UGX 200,000 / night',
                    'icon'    => '🏪',
                    'url'     => 'https://www.sanjosehotelkampala.com',
                ],
                [
                    'name'    => 'Hotel Africana',
                    'desc'    => 'Well-known Kampala hotel near the city centre. ~10 km from venue.',
                    'price'   => 'From UGX 180,000 / night',
                    'icon'    => '🌍',
                    'url'     => 'https://www.hotelafrican.com',
                ],
                [
                    'name'    => 'St Mbaga Hotel',
                    'desc'    => 'Budget-friendly and conveniently located near Gaba Road.',
                    'price'   => 'From UGX 120,000 / night',
                    'icon'    => '🏠',
                    'url'     => 'https://www.google.com/search?q=St+Mbaga+Hotel+Kampala',
                ],
                [
                    'name'    => 'GCC Guest House',
                    'desc'    => 'On-site guest house at Gaba Community Church. Limited rooms — book early.',
                    'price'   => 'From UGX 80,000 / night',
                    'icon'    => '⛪',
                    'url'     => 'mailto:info@renewalsummit.ug',
                ],
            ];
            @endphp

            @foreach($hotels as $hotel)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-6 flex-1">
                    <div class="text-3xl mb-3">{{ $hotel['icon'] }}</div>
                    <h3 class="font-extrabold text-summit text-lg leading-tight mb-1">{{ $hotel['name'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed mb-3">{{ $hotel['desc'] }}</p>
                    <span class="inline-block bg-yellow-50 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full">
                        {{ $hotel['price'] }}
                    </span>
                </div>
                <div class="px-6 pb-5">
                    <a href="{{ $hotel['url'] }}" target="_blank" rel="noopener noreferrer"
                       class="block w-full text-center bg-summit hover:opacity-80 text-white font-bold py-2.5 rounded-xl text-sm transition">
                        Book Now →
                    </a>
                </div>
            </div>
            @endforeach

        </div>

        <p class="text-center text-xs text-gray-400 mt-8">
            Prices are approximate. Please contact hotels directly for availability and group rates.
            For the GCC Guest House or transport enquiries, email <a href="mailto:renewalsummit@africarenewal.org" class="underline text-gold">renewalsummit@africarenewal.org</a>.
        </p>
    </div>
</section>

{{-- ── DONATE CTA ────────────────────────────────────────────────── --}}
<section class="py-16 bg-summit text-white">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <div class="text-5xl mb-4">🙏</div>
        <h2 class="text-3xl font-extrabold mb-4">Support the Summit</h2>
        <p class="text-gray-300 leading-relaxed mb-8">
            Your donation helps cover costs for delegates from underserved regions, event logistics,
            and the overall success of this global gathering.
        </p>
        <a href="{{ route('donate') }}"
           class="bg-gold hover:bg-yellow-600 text-white font-bold px-8 py-4 rounded-xl text-lg transition shadow-xl inline-block">
            Donate Towards the Summit →
        </a>
    </div>
</section>

{{-- ── REGISTER CTA ──────────────────────────────────────────────── --}}
<section class="py-16 bg-gradient-to-r from-yellow-50 to-white">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-extrabold text-summit mb-4">Ready to Join Us?</h2>
        <p class="text-gray-600 mb-8">
            Secure your spot at Renewal Summit 2026. Registration requires payment via
            Mobile Money (MTN/Airtel) or VISA card.
        </p>
        <a href="{{ route('register.start') }}"
           class="bg-summit hover:bg-blue-900 text-white font-bold px-8 py-4 rounded-xl text-lg transition shadow-xl inline-block">
            Start Registration →
        </a>
        <div class="mt-4 text-sm text-gray-500">
            Already started?
            <a href="{{ route('register.start') }}#resume" class="text-gold hover:underline font-semibold">Resume your registration</a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
// ── Countdown Timer ──────────────────────────────────────────────
(function () {
    const target = new Date('2026-08-17T00:00:00').getTime();

    function pad(n) { return String(Math.floor(n)).padStart(2, '0'); }

    function tick() {
        const now  = Date.now();
        const diff = Math.max(0, target - now);

        const totalSeconds = Math.floor(diff / 1000);
        const seconds = totalSeconds % 60;
        const totalMinutes = Math.floor(totalSeconds / 60);
        const minutes = totalMinutes % 60;
        const totalHours = Math.floor(totalMinutes / 60);
        const hours = totalHours % 24;
        const totalDays = Math.floor(totalHours / 24);
        const weeks = Math.floor(totalDays / 7);
        const days  = totalDays % 7;

        document.getElementById('cd-weeks').textContent   = pad(weeks);
        document.getElementById('cd-days').textContent    = pad(days);
        document.getElementById('cd-hours').textContent   = pad(hours);
        document.getElementById('cd-minutes').textContent = pad(minutes);
        document.getElementById('cd-seconds').textContent = pad(seconds);
    }

    tick();
    setInterval(tick, 1000);
})();
(function () {
    function animateCountUp(el) {
        const target = parseInt(el.dataset.countup, 10);
        const suffix = el.dataset.suffix || '';
        const duration = 3200; // ms
        const start = performance.now();

        function easeOutQuart(t) { return 1 - Math.pow(1 - t, 4); }

        function step(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const value = Math.round(easeOutQuart(progress) * target);
            el.textContent = value.toLocaleString() + suffix;
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.querySelectorAll('[data-countup]').forEach(animateCountUp);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.3 });

    const bar = document.getElementById('stats-bar');
    if (bar) observer.observe(bar);
})();
</script>
@endpush
