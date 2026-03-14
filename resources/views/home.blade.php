@extends('layouts.public')
@section('title', 'Renewal Summit 2026 – Healthy Church')

@section('content')

{{-- ── HERO ──────────────────────────────────────────────────────── --}}
<section class="relative bg-summit text-white overflow-hidden"
         style="min-height:70vh; background-image: linear-gradient(to bottom right, #1a1a2e 60%, #16213e);">

    {{-- Decorative gold bar --}}
    <div class="absolute inset-y-0 right-0 w-1/3 opacity-5"
         style="background: radial-gradient(circle, #D4A017 0%, transparent 70%)"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col-reverse lg:flex-row items-center justify-between
                py-10 lg:py-20 gap-8 lg:gap-12 relative z-10">

        {{-- Text --}}
        <div class="lg:w-1/2 text-center lg:text-left">
            <p class="uppercase tracking-widest text-yellow-400 text-sm font-semibold mb-3">
                International Conference
            </p>
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold leading-tight mb-4">
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
                    Get Tickets
                </a>
                <a href="{{ route('donate') }}"
                   class="border-2 border-yellow-400 text-yellow-400 hover:bg-yellow-400 hover:text-summit
                          font-bold px-8 py-4 rounded-xl text-lg transition inline-block text-center">
                    Donate
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
                <a href="https://www.google.com/calendar/render?action=TEMPLATE&text=Renewal+Summit+2026&dates=20260817T000000Z/20260827T235959Z&details=International+Conference+%E2%80%93+Healthy+Church.+A+global+gathering+for+pastors+and+leaders+from+27+nations.&location=Ggaba+Community+Church%2C+Kampala%2C+Uganda&sf=true&output=xml"
                   target="_blank" rel="noopener"
                   class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-yellow-400/40
                          rounded-lg px-4 py-2 text-sm transition font-semibold text-yellow-300">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5C3.9 4 3 4.9 3 6v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                    </svg>
                    Add to Google Calendar
                </a>
            </div>
        </div>

        {{-- Hero image area --}}
        <div class="lg:w-1/2 flex justify-center">
            <div class="relative">
                <div class="w-64 h-72 sm:w-80 sm:h-96 lg:w-96 lg:h-[480px] rounded-2xl overflow-hidden shadow-2xl border-4 border-yellow-400/40">
                    <img src="{{ asset('images/together.jpg') }}" alt="Renewal Summit 2026"
                         class="w-full h-full object-cover">
                </div>
                {{-- Save the date badge --}}
                <a href="https://www.google.com/calendar/render?action=TEMPLATE&text=Renewal+Summit+2026&dates=20260817T000000Z/20260827T235959Z&details=International+Conference+%E2%80%93+Healthy+Church.+A+global+gathering+for+pastors+and+leaders+from+27+nations.+Register+at+renewalsummit.africarenewal.org&location=Ggaba+Community+Church%2C+Kampala%2C+Uganda&sf=true&output=xml"
                   target="_blank" rel="noopener"
                   class="absolute -top-4 -right-4 bg-gold hover:bg-yellow-600 text-white rounded-full w-20 h-20 flex flex-col
                          items-center justify-center text-center shadow-xl font-bold text-xs transition"
                   title="Save to Google Calendar">
                    SAVE THE<br>DATE
                </a>
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
        <h2 class="text-white font-extrabold text-lg sm:text-2xl mb-8 px-2">August 17, 2026 · Ggaba Community Church, Uganda</h2>
        <div class="grid grid-cols-3 sm:grid-cols-5 gap-3 max-w-lg sm:max-w-2xl mx-auto" id="countdown">
            @foreach(['weeks','days','hours','minutes','seconds'] as $unit)
            <div class="flex flex-col items-center gap-2">
                <div class="w-full aspect-square rounded-2xl bg-white/10 border border-white/20 shadow-xl
                            flex items-center justify-center">
                    <span class="text-3xl sm:text-5xl font-extrabold text-white tabular-nums"
                          id="cd-{{ $unit }}">00</span>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-yellow-400">{{ $unit }}</span>
            </div>
            @endforeach
        </div>

        <div class="mt-10">
            <a href="https://www.google.com/calendar/render?action=TEMPLATE&text=Renewal+Summit+2026&dates=20260817T000000Z/20260827T235959Z&details=International+Conference+%E2%80%93+Healthy+Church.+A+global+gathering+for+pastors+and+leaders+from+27+nations.+Register+at+renewalsummit.africarenewal.org&location=Ggaba+Community+Church%2C+Kampala%2C+Uganda&sf=true&output=xml"
               target="_blank" rel="noopener"
               class="inline-flex items-center gap-3 bg-white text-summit font-bold px-7 py-3.5 rounded-xl
                      shadow-lg hover:bg-yellow-50 transition text-sm">
                <img src="https://www.gstatic.com/calendar/images/dynamiclogo_2020q4/calendar_31_2x.png"
                     alt="Google Calendar" class="w-6 h-6 rounded">
                Save the Date in Google Calendar
            </a>
        </div>
    </div>
</section>

{{-- ── STATS BAR ─────────────────────────────────────────────────── --}}
<div class="bg-gold text-white" id="stats-bar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-wrap justify-center gap-8">
        <div class="flex flex-col items-center gap-3">
            <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-white/40 bg-white/10 flex items-center justify-center shadow-lg">
                <span class="text-3xl sm:text-4xl font-extrabold" data-countup="1500" data-suffix="+">0</span>
            </div>
            <div class="text-xs sm:text-sm font-semibold uppercase tracking-widest opacity-90">Leaders</div>
        </div>
        <div class="flex flex-col items-center gap-3">
            <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-white/40 bg-white/10 flex items-center justify-center shadow-lg">
                <span class="text-3xl sm:text-4xl font-extrabold" data-countup="27">0</span>
            </div>
            <div class="text-xs sm:text-sm font-semibold uppercase tracking-widest opacity-90">Nations</div>
        </div>
        <div class="flex flex-col items-center gap-3">
            <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-white/40 bg-white/10 flex items-center justify-center shadow-lg">
                <span class="text-3xl sm:text-4xl font-extrabold" data-countup="5">0</span>
            </div>
            <div class="text-xs sm:text-sm font-semibold uppercase tracking-widest opacity-90">Days</div>
        </div>
    </div>
</div>

{{-- ── ABOUT ─────────────────────────────────────────────────────── --}}
<section id="about" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="bg-gold/10 text-yellow-700 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full">About</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-summit mt-4">About Renewal Summit 2026</h2>
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
                    Hosted at <strong>Ggaba Community Church (GCC), Uganda</strong>, this 5-days conference
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
<section class="py-16 bg-gray-50" id="gallery">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center mb-10">
        <span class="bg-gold/10 text-yellow-700 text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-full">Gallery</span>
        <h2 class="text-3xl font-extrabold text-summit mt-3">A Taste of Renewal Summit</h2>
        <p class="text-gray-500 mt-2 text-sm">Moments from previous summits — worship, training, evenings and community.</p>
    </div>

    {{-- Tab filters --}}
    <div class="flex flex-wrap justify-center gap-2 mb-10">
        <button class="gtab active-tab px-5 py-2 rounded-full text-sm font-semibold border transition" data-panel="panel-all">All</button>
        <button class="gtab px-5 py-2 rounded-full text-sm font-semibold border transition" data-panel="panel-worship">🙌 Worship</button>
        <button class="gtab px-5 py-2 rounded-full text-sm font-semibold border transition" data-panel="panel-training">📚 Training &amp; Sessions</button>
        <button class="gtab px-5 py-2 rounded-full text-sm font-semibold border transition" data-panel="panel-evenings">🌙 Evening Entertainment</button>
        <button class="gtab px-5 py-2 rounded-full text-sm font-semibold border transition" data-panel="panel-community">🤝 Community</button>
    </div>

    @php
    $groups = [
        [
            'id'    => 'worship',
            'label' => '🙌 Worship',
            'color' => 'bg-purple-700',
            'images'=> [
                ['src' => 'praise1.jpg',      'caption' => 'Praise & Worship'],
                ['src' => 'holycommunion.jpg', 'caption' => 'Holy Communion'],
                ['src' => 'performance1.jpg',  'caption' => 'Worship Performance'],
            ],
        ],
        [
            'id'    => 'training',
            'label' => '📚 Training & Sessions',
            'color' => 'bg-blue-700',
            'images'=> [
                ['src' => 'trainings.jpg',  'caption' => 'Training Sessions'],
                ['src' => 'training2.jpg',  'caption' => 'Workshop'],
                ['src' => 'training3.jpg',  'caption' => 'Breakout Session'],
                ['src' => 'training4.jpg',  'caption' => 'Plenary Session'],
            ],
        ],
        [
            'id'    => 'evenings',
            'label' => '🌙 Evening Entertainment',
            'color' => 'bg-indigo-800',
            'images'=> [
                ['src' => 'evening1.jpg',     'caption' => 'Evening Entertainment'],
                ['src' => 'evening2.jpg',      'caption' => 'Evening Programme'],
                ['src' => 'performance2.jpg',  'caption' => 'Live Performance'],
                ['src' => 'performance3.jpg',  'caption' => 'Evening Show'],
            ],
        ],
        [
            'id'    => 'community',
            'label' => '🤝 Community',
            'color' => 'bg-green-700',
            'images'=> [
                ['src' => 'together.jpg',  'caption' => 'Together'],
                ['src' => 'together1.jpeg',  'caption' => 'Summit Community'],
            ],
        ],
    ];
    @endphp

    {{-- ── ALL PANEL ───────────────────────────────────────────── --}}
    <div id="panel-all" class="gpanel">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($groups as $group)
                @foreach($group['images'] as $img)
                <div class="relative rounded-2xl overflow-hidden shadow-md group cursor-pointer">
                    <img src="{{ asset('images/' . $img['src']) }}"
                         alt="{{ $img['caption'] }}"
                         class="w-full h-52 object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-3">
                        <p class="text-white font-bold text-xs drop-shadow leading-tight">{{ $img['caption'] }}</p>
                        <span class="text-white/60 text-xs">{{ $group['label'] }}</span>
                    </div>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- ── CATEGORY CAROUSEL PANELS ────────────────────────────── --}}
    @foreach($groups as $group)
    <div id="panel-{{ $group['id'] }}" class="gpanel hidden">

        {{-- Category badge + slide counter --}}
        <div class="flex items-center justify-between mb-5">
            <span class="text-white text-sm font-bold px-4 py-1.5 rounded-full {{ $group['color'] }}">{{ $group['label'] }}</span>
            <span class="text-gray-400 text-sm" id="counter-{{ $group['id'] }}">1 / {{ count($group['images']) }}</span>
        </div>

        {{-- Carousel container --}}
        <div class="relative rounded-2xl overflow-hidden shadow-lg" style="height: 480px;">

            {{-- Slides (absolutely stacked) --}}
            @foreach($group['images'] as $i => $img)
            <div class="gslide absolute inset-0 transition-opacity duration-500 ease-in-out {{ $i === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}"
                 id="slide-{{ $group['id'] }}-{{ $i }}">
                <img src="{{ asset('images/' . $img['src']) }}"
                     alt="{{ $img['caption'] }}"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6">
                    <p class="text-white font-extrabold text-xl drop-shadow">{{ $img['caption'] }}</p>
                    <p class="text-gray-300 text-sm mt-1">Renewal Summit 2026</p>
                </div>
            </div>
            @endforeach

            {{-- Prev / Next arrows (centred vertically) --}}
            <button onclick="goPrev('{{ $group['id'] }}')"
                    class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-black/40 hover:bg-summit text-white text-2xl flex items-center justify-center transition backdrop-blur-sm">
                &#8249;
            </button>
            <button onclick="goNext('{{ $group['id'] }}')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-black/40 hover:bg-summit text-white text-2xl flex items-center justify-center transition backdrop-blur-sm">
                &#8250;
            </button>

        </div>

        {{-- Dots --}}
        <div class="flex justify-center gap-2 mt-4" id="dots-{{ $group['id'] }}">
            @foreach($group['images'] as $i => $img)
            <button onclick="goSlide('{{ $group['id'] }}', {{ $i }})"
                    class="gdot w-3 h-3 rounded-full border-2 border-summit transition-all {{ $i === 0 ? 'bg-summit scale-110' : 'bg-white' }}"
                    data-i="{{ $i }}"></button>
            @endforeach
        </div>

        {{-- Thumbnail strip --}}
        <div class="grid gap-2 mt-4" style="grid-template-columns: repeat({{ count($group['images']) }}, minmax(0, 1fr))">
            @foreach($group['images'] as $i => $img)
            <div onclick="goSlide('{{ $group['id'] }}', {{ $i }})"
                 class="gthumb relative rounded-xl overflow-hidden cursor-pointer ring-2 ring-transparent transition {{ $i === 0 ? 'ring-summit' : '' }}"
                 id="thumb-{{ $group['id'] }}-{{ $i }}">
                <img src="{{ asset('images/' . $img['src']) }}"
                     alt="{{ $img['caption'] }}"
                     class="w-full h-20 object-cover">
                <div class="absolute inset-0 {{ $i === 0 ? '' : 'bg-black/30' }}" id="thumb-overlay-{{ $group['id'] }}-{{ $i }}"></div>
            </div>
            @endforeach
        </div>

    </div>
    @endforeach

</div>
</section>

@push('scripts')
<script>
(function () {
    // ── Per-group slide state ────────────────────────────────────
    var cur = {};

    function getSlides(id) {
        return document.querySelectorAll('[id^="slide-' + id + '-"]');
    }
    function getDots(id) {
        return document.querySelectorAll('#dots-' + id + ' .gdot');
    }
    function getThumbs(id) {
        return document.querySelectorAll('[id^="thumb-' + id + '-"]');
    }

    function updateCarousel(id) {
        var idx    = cur[id] || 0;
        var slides = getSlides(id);
        var dots   = getDots(id);
        var total  = slides.length;

        // Counter
        var ctr = document.getElementById('counter-' + id);
        if (ctr) ctr.textContent = (idx + 1) + ' / ' + total;

        slides.forEach(function (s, i) {
            if (i === idx) {
                s.classList.remove('opacity-0', 'z-0');
                s.classList.add('opacity-100', 'z-10');
            } else {
                s.classList.remove('opacity-100', 'z-10');
                s.classList.add('opacity-0', 'z-0');
            }
        });

        dots.forEach(function (d, i) {
            d.classList.toggle('bg-summit',  i === idx);
            d.classList.toggle('scale-110',  i === idx);
            d.classList.toggle('bg-white',   i !== idx);
        });

        // Thumbnails
        for (var i = 0; i < total; i++) {
            var thumb = document.getElementById('thumb-' + id + '-' + i);
            var overlay = document.getElementById('thumb-overlay-' + id + '-' + i);
            if (thumb) {
                thumb.classList.toggle('ring-summit', i === idx);
                thumb.classList.toggle('ring-transparent', i !== idx);
            }
            if (overlay) {
                overlay.className = 'absolute inset-0 ' + (i === idx ? '' : 'bg-black/30');
            }
        }
    }

    window.goSlide = function (id, idx) {
        var slides = getSlides(id);
        cur[id] = ((idx % slides.length) + slides.length) % slides.length;
        updateCarousel(id);
    };

    window.goNext = function (id) {
        var slides = getSlides(id);
        cur[id] = (((cur[id] || 0) + 1) % slides.length);
        updateCarousel(id);
    };

    window.goPrev = function (id) {
        var slides = getSlides(id);
        cur[id] = (((cur[id] || 0) - 1 + slides.length) % slides.length);
        updateCarousel(id);
    };

    // Auto-advance every 5 s for each group
    ['worship', 'training', 'evenings', 'community'].forEach(function (id) {
        cur[id] = 0;
        setInterval(function () {
            var panel = document.getElementById('panel-' + id);
            if (panel && !panel.classList.contains('hidden')) {
                goNext(id);
            }
        }, 5000);
    });

    // ── Tab / panel switching ────────────────────────────────────
    var tabs   = document.querySelectorAll('.gtab');
    var panels = document.querySelectorAll('.gpanel');

    function activateTab(btn) {
        tabs.forEach(function (t) {
            t.classList.remove('bg-summit', 'text-white', 'border-summit');
            t.classList.add('bg-white', 'text-gray-600', 'border-gray-200');
        });
        btn.classList.remove('bg-white', 'text-gray-600', 'border-gray-200');
        btn.classList.add('bg-summit', 'text-white', 'border-summit');
    }

    tabs.forEach(function (btn) {
        // Default styling
        btn.classList.add('bg-white', 'text-gray-600', 'border-gray-200');

        btn.addEventListener('click', function () {
            var target = btn.dataset.panel;
            activateTab(btn);
            panels.forEach(function (p) {
                if (p.id === target) {
                    p.classList.remove('hidden');
                } else {
                    p.classList.add('hidden');
                }
            });
        });
    });

    // Init: show All tab active
    var allBtn = document.querySelector('.gtab[data-panel="panel-all"]');
    if (allBtn) activateTab(allBtn);
})();
</script>
@endpush

{{-- ── SCHEDULE ──────────────────────────────────────────────────── --}}
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-2xl sm:text-4xl font-extrabold text-summit">Programme Schedule</h2>
            <p class="text-gray-500 mt-2">August 17–21, 2026</p>
        </div>
        <div class="space-y-4">
            @foreach([
                ['Mon Aug 17', 'Arrival & Registration',      'Check-in, welcome reception and opening worship'],
                ['Tue Aug 18', 'Spiritual Markers',           'Morning sessions, workshops, evening celebration'],
                ['Wed Aug 19', 'Relational Markers',          'Seminars, breakout sessions, networking dinner'],
                ['Thu Aug 20', 'Missional Markers',           'Keynotes, outreach activation, prayer sessions'],
                ['Fri Aug 21', 'Commissioning & Closing',     'Final session, commissioning service, departure'],
            ] as [$day, $title, $desc])
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 bg-white rounded-xl p-4 sm:p-5 shadow-sm hover:shadow-md transition">
                    <div class="sm:min-w-[100px] text-left sm:text-center">
                        <div class="inline-block bg-summit text-white text-xs font-bold px-2 py-1 rounded">{{ $day }}</div>
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

{{-- ── SPEAKERS ─────────────────────────────────────────────────── --}}
<section class="py-20 bg-white" id="speakers">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section header --}}
        <div class="text-center mb-14">
            <p class="uppercase tracking-widest text-yellow-500 text-sm font-bold mb-2">Renewal Summit 2026</p>
            <h2 class="text-4xl font-extrabold text-gray-900 mb-3">Speakers &amp; International Guests</h2>
            <div class="mx-auto w-16 h-1 bg-yellow-400 rounded"></div>
        </div>

        {{-- A. PLENARY SPEAKERS --}}
        <h3 class="text-lg font-bold uppercase tracking-widest text-gray-500 mb-8 border-b border-gray-100 pb-3">
            A. Plenary Speakers
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-16">

            @php
            $plenary = [
                ['name' => 'Rev. Peter Kasirivu',  'role' => 'Host',             'church' => '',                         'country' => 'Uganda',  'img' => null, 'badge' => 'Host'],
                ['name' => 'Dr. Paul David Tripp', 'role' => 'Plenary Speaker',  'church' => 'Paul Tripp Ministries',    'country' => 'USA',     'img' => null, 'badge' => null],
                ['name' => 'Ps. Michael Yearley',  'role' => 'Keynote Speaker',  'church' => 'Rocky Peak',               'country' => 'USA',     'img' => null, 'badge' => 'Keynote'],
                ['name' => 'Ps. Brad Thomas',      'role' => 'Keynote Speaker',  'church' => 'Austin Ridge',             'country' => 'USA',     'img' => null, 'badge' => 'Keynote'],
                ['name' => 'Ps. Eric Geiger',      'role' => 'Plenary Speaker',  'church' => 'Mariners Church',          'country' => 'USA',     'img' => null, 'badge' => 'Pending'],
                ['name' => 'Ps. Bradley Goode',    'role' => 'Plenary Speaker',  'church' => 'Good News Church',         'country' => 'USA',     'img' => null, 'badge' => null],
                ['name' => 'Ps. Jose Jazas',       'role' => 'Plenary Speaker',  'church' => '26 West Church',           'country' => 'USA',     'img' => null, 'badge' => null],
                ['name' => 'Ps. Jason Uptmore',    'role' => 'Plenary Speaker',  'church' => 'Wayside Chapel',           'country' => 'USA',     'img' => null, 'badge' => null],
                ['name' => 'Ps. Bryant Lee',       'role' => 'Plenary Speaker',  'church' => 'Higher Expectation Church','country' => 'USA',     'img' => null, 'badge' => null],
                ['name' => 'Bp. Oscar Muriu',      'role' => 'Plenary Speaker',  'church' => 'Nairobi Chapel',           'country' => 'Kenya',   'img' => null, 'badge' => null],
                ['name' => 'Ps. Mark Strong',      'role' => 'Plenary Speaker',  'church' => '',                         'country' => '',        'img' => null, 'badge' => null],
                ['name' => 'Ps. Julius Rwotlonyo', 'role' => 'Plenary Speaker',  'church' => 'Watoto Church',            'country' => 'Uganda',  'img' => null, 'badge' => null],
                ['name' => 'Ps. Curtis Bronzan',   'role' => 'Plenary Speaker',  'church' => 'FPC Houston',              'country' => 'USA',     'img' => null, 'badge' => null],
                ['name' => 'Woman Speaker',         'role' => 'Plenary Speaker',  'church' => '',                         'country' => '',        'img' => null, 'badge' => 'TBA'],
            ];
            @endphp

            @foreach($plenary as $s)
            <div class="flex flex-col items-center text-center group">
                {{-- Avatar --}}
                <div class="relative mb-4">
                    @if($s['img'])
                        <img src="{{ asset('images/speakers/' . $s['img']) }}"
                             alt="{{ $s['name'] }}"
                             class="w-28 h-28 rounded-full object-cover object-top border-4 border-white shadow-lg
                                    group-hover:border-yellow-400 transition-all duration-300">
                    @else
                        <div class="w-28 h-28 rounded-full bg-gradient-to-br from-slate-700 to-slate-900
                                    flex items-center justify-center border-4 border-white shadow-lg
                                    group-hover:border-yellow-400 transition-all duration-300 text-white text-2xl font-bold select-none">
                            {{ collect(explode(' ', $s['name']))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->implode('') }}
                        </div>
                    @endif
                    {{-- Badge --}}
                    @if($s['badge'])
                        @php
                            $badgeColor = match($s['badge']) {
                                'Keynote'  => 'bg-yellow-400 text-gray-900',
                                'Host'     => 'bg-red-600 text-white',
                                'Pending'  => 'bg-gray-400 text-white',
                                'TBA'      => 'bg-gray-300 text-gray-600',
                                default    => 'bg-blue-500 text-white',
                            };
                        @endphp
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 text-xs font-bold px-2 py-0.5 rounded-full whitespace-nowrap {{ $badgeColor }}">
                            {{ $s['badge'] }}
                        </span>
                    @endif
                </div>
                <p class="font-bold text-gray-900 text-sm leading-snug">{{ $s['name'] }}</p>
                @if($s['church'])
                    <p class="text-xs text-yellow-600 font-medium mt-0.5">{{ $s['church'] }}</p>
                @endif
                @if($s['country'])
                    <p class="text-xs text-gray-400 mt-0.5">{{ $s['country'] }}</p>
                @endif
            </div>
            @endforeach
        </div>

        {{-- B. BREAKOUT SESSIONS --}}
        <h3 class="text-lg font-bold uppercase tracking-widest text-gray-500 mb-8 border-b border-gray-100 pb-3">
            B. Breakout Sessions
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-6 mb-16">

            @php
            $breakout = [
                ['name' => 'Tony Bowick',           'role' => 'Breakout Speaker', 'church' => 'Rocky Peak',  'country' => 'USA',      'img' => null],
                ['name' => 'Dr. Augustin Longa',    'role' => 'Breakout Speaker', 'church' => '',            'country' => 'Cameroon', 'img' => null],
                ['name' => 'Bp. Christopher Mukwavi','role'=> 'Breakout Speaker', 'church' => '',            'country' => 'Zambia',   'img' => null],
                ['name' => 'Ps. Bernard Mukwavi',   'role' => 'Breakout Speaker', 'church' => '',            'country' => 'Canada',   'img' => null],
                ['name' => 'Ps. Brian Morehead',    'role' => 'Breakout Speaker', 'church' => 'Rocky Peak',  'country' => 'USA',      'img' => null],
            ];
            @endphp

            @foreach($breakout as $s)
            <div class="flex flex-col items-center text-center group">
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-yellow-700 to-yellow-900
                            flex items-center justify-center border-4 border-white shadow-md
                            group-hover:border-yellow-400 transition-all duration-300 text-white text-xl font-bold mb-3 select-none">
                    {{ collect(explode(' ', $s['name']))->map(fn($w) => strtoupper(substr($w,0,1)))->take(2)->implode('') }}
                </div>
                <p class="font-bold text-gray-900 text-sm leading-snug">{{ $s['name'] }}</p>
                @if($s['church'])
                    <p class="text-xs text-yellow-600 font-medium mt-0.5">{{ $s['church'] }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-0.5">{{ $s['country'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- C. PANELS --}}
        <h3 class="text-lg font-bold uppercase tracking-widest text-gray-500 mb-6 border-b border-gray-100 pb-3">
            C. Panels
        </h3>
        <div class="flex items-center justify-center bg-gray-50 rounded-2xl py-10 mb-16 text-center">
            <div>
                <p class="text-gray-400 text-sm font-medium uppercase tracking-widest mb-1">Coming Soon</p>
                <p class="text-gray-500 text-base">Panel speakers will be announced shortly.</p>
            </div>
        </div>

        {{-- D. INTERNATIONAL GUESTS --}}
        <h3 class="text-lg font-bold uppercase tracking-widest text-gray-500 mb-6 border-b border-gray-100 pb-3">
            D. International Guests
        </h3>
        <div class="grid sm:grid-cols-2 gap-4 mb-4">
            <div class="flex items-start gap-4 bg-gray-50 rounded-xl p-5 border border-gray-100">
                <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold text-lg shrink-0">DTS</div>
                <div>
                    <p class="font-bold text-gray-900">Dallas Theological Seminary</p>
                    <p class="text-xs text-gray-500 mt-0.5">A team joining us from Dallas, USA</p>
                </div>
            </div>
            <div class="flex items-start gap-4 bg-gray-50 rounded-xl p-5 border border-gray-100">
                <div class="w-12 h-12 rounded-full bg-yellow-500 flex items-center justify-center text-white font-bold text-lg shrink-0">🌍</div>
                <div>
                    <p class="font-bold text-gray-900">Global Partners &amp; Delegates</p>
                    <p class="text-xs text-gray-500 mt-0.5">Representatives and partners from many nations</p>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ── REGISTER & DONATE CTA ─────────────────────────────────────── --}}
<section class="py-20 bg-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 rounded-3xl overflow-hidden shadow-2xl">

            {{-- LEFT: Register --}}
            <div class="bg-summit text-white px-10 py-16 flex flex-col items-center text-center">
                <div class="text-5xl mb-5">🎟️</div>
                <h2 class="text-2xl sm:text-3xl font-extrabold mb-3 text-white">Ready to Join Us?</h2>
                <p class="text-gray-300 text-sm leading-relaxed mb-8 max-w-xs">
                    Secure your spot at Renewal Summit 2026. Payment via Mobile Money (MTN/Airtel) or VISA card.
                </p>
                <a href="{{ route('register.start') }}"
                   class="bg-gold hover:bg-yellow-500 text-white font-bold px-8 py-4 rounded-xl text-base
                          transition shadow-xl inline-block w-full max-w-xs text-center">
                    Start Registration →
                </a>
                <p class="mt-5 text-xs text-gray-400">
                    Already started?
                    <a href="{{ route('register.start') }}#resume" class="text-yellow-400 hover:underline font-semibold">Resume your registration</a>
                </p>
            </div>

            {{-- RIGHT: Donate --}}
            <div class="bg-gold text-white px-10 py-16 flex flex-col items-center text-center">
                <div class="text-5xl mb-5">🙏</div>
                <h2 class="text-2xl sm:text-3xl font-extrabold mb-3 text-white">Support the Summit</h2>
                <p class="text-yellow-100 text-sm leading-relaxed mb-8 max-w-xs">
                    Your donation helps cover costs for delegates from underserved regions, event logistics,
                    and the overall success of this global gathering.
                </p>
                <a href="{{ route('donate') }}"
                   class="bg-white hover:bg-gray-100 text-yellow-600 font-bold px-7 py-4 rounded-xl text-base
                          transition shadow-xl inline-block w-full max-w-xs text-center">
                    Donate Towards the Summit →
                </a>
            </div>

        </div>
    </div>
</section>


{{-- ── ACCOMMODATION ────────────────────────────────────────────── --}}
<section class="py-16 bg-gray-50" id="accommodation">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-12">
            <span class="text-gold font-bold uppercase tracking-widest text-xs">Where To Stay</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-summit mt-2">Recommended Accommodation</h2>
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
