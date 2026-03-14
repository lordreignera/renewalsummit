@extends('layouts.public')
@section('title', 'Renewal Summit 2026 – Healthy Church')

@section('content')

{{-- ── HERO ──────────────────────────────────────────────────────── --}}
<style>
@keyframes heroFadeUp   { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:translateY(0); } }
@keyframes heroScrollBob{ 0%,100%{transform:translateX(-50%) translateY(0)} 50%{transform:translateX(-50%) translateY(8px)} }
@keyframes heroPulse    { 0%,100%{box-shadow:0 0 0 0 rgba(212,160,23,.5)}  70%{box-shadow:0 0 0 14px rgba(212,160,23,0)} }
.hero-fadeup-1{ animation:heroFadeUp .9s ease both .1s }
.hero-fadeup-2{ animation:heroFadeUp .9s ease both .35s }
.hero-fadeup-3{ animation:heroFadeUp .9s ease both .55s }
.hero-fadeup-4{ animation:heroFadeUp .9s ease both .75s }
.hero-fadeup-5{ animation:heroFadeUp .9s ease both .95s }
.hero-cta-primary{ display:inline-flex;align-items:center;gap:.7rem;background:#D4A017;color:#fff;font-weight:800;padding:1.1rem 2.8rem;border-radius:50px;text-decoration:none;font-size:1.15rem;letter-spacing:.04em;box-shadow:0 4px 24px rgba(212,160,23,.45);transition:background .2s,transform .2s,box-shadow .2s; animation:heroPulse 2.5s infinite .3s; }
.hero-cta-primary:hover{ background:#b8880f;transform:translateY(-2px);box-shadow:0 8px 32px rgba(212,160,23,.55); }
.hero-cta-secondary{ display:inline-flex;align-items:center;gap:.7rem;background:transparent;color:#fff;font-weight:700;padding:1.1rem 2.8rem;border-radius:50px;text-decoration:none;font-size:1.15rem;border:2px solid rgba(255,255,255,.5);transition:background .2s,border-color .2s,transform .2s; }
.hero-cta-secondary:hover{ background:rgba(255,255,255,.12);border-color:#D4A017;transform:translateY(-2px); }
.hero-dot{ height:4px;border-radius:2px;border:none;cursor:pointer;transition:width .4s,background .4s;padding:0; }
</style>

<section id="hero-section" style="position:relative;min-height:100vh;overflow:hidden;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;">

    {{-- Background slides (cross-fade) --}}
    <div id="hero-bg-0" style="position:absolute;inset:0;background-image:url('{{ asset('images/international.jpg') }}');background-size:cover;background-position:center;opacity:1;transition:opacity 1.8s ease-in-out;z-index:0;"></div>
    <div id="hero-bg-1" style="position:absolute;inset:0;background-image:url('{{ asset('images/together2.jpg') }}');background-size:cover;background-position:center;opacity:0;transition:opacity 1.8s ease-in-out;z-index:0;"></div>
    <div id="hero-bg-2" style="position:absolute;inset:0;background-image:url('{{ asset('images/pannel_-24.jpg') }}');background-size:cover;background-position:center top;opacity:0;transition:opacity 1.8s ease-in-out;z-index:0;"></div>
    <div id="hero-bg-3" style="position:absolute;inset:0;background-image:url('{{ asset('images/holycommunion.jpg') }}');background-size:cover;background-position:center;opacity:0;transition:opacity 1.8s ease-in-out;z-index:0;"></div>
    <div id="hero-bg-4" style="position:absolute;inset:0;background-image:url('{{ asset('images/trainings.jpg') }}');background-size:cover;background-position:center;opacity:0;transition:opacity 1.8s ease-in-out;z-index:0;"></div>
    <div id="hero-bg-5" style="position:absolute;inset:0;background-image:url('{{ asset('images/together1.jpg') }}');background-size:cover;background-position:center;opacity:0;transition:opacity 1.8s ease-in-out;z-index:0;"></div>

    {{-- Cinematic gradient overlay – lighter so photos show clearly --}}
    <div style="position:absolute;inset:0;background:linear-gradient(to bottom, rgba(10,22,52,.72) 0%, rgba(10,22,52,.30) 35%, rgba(10,22,52,.30) 65%, rgba(10,22,52,.80) 100%);z-index:1;"></div>

    {{-- Main content --}}
    <div style="position:relative;z-index:2;max-width:820px;margin:0 auto;padding:6rem 1.5rem 7rem;">

        {{-- Label with decorative lines --}}
        <div class="hero-fadeup-1" style="display:flex;align-items:center;justify-content:center;gap:1rem;margin-bottom:2.4rem;">
            <div style="height:1px;width:70px;background:linear-gradient(to right,transparent,#D4A017);"></div>
            <span style="color:#D4A017;font-size:1rem;font-weight:700;letter-spacing:.28em;text-transform:uppercase;">International Conference</span>
            <div style="height:1px;width:70px;background:linear-gradient(to left,transparent,#D4A017);"></div>
        </div>

        {{-- Logo – glow filter instead of white box --}}
        <div class="hero-fadeup-2" style="margin-bottom:2.2rem;">
            <img src="{{ asset('images/summit26.png') }}" alt="Renewal Summit 2026"
                 style="height:300px;width:auto;object-fit:contain;display:inline-block;
                        filter:drop-shadow(0 0 40px rgba(255,255,255,.95)) drop-shadow(0 0 20px rgba(212,160,23,.8)) drop-shadow(0 8px 32px rgba(0,0,0,.9));">
        </div>

        {{-- Theme pill --}}
        <div class="hero-fadeup-3" style="margin-bottom:2rem;">
            <span style="display:inline-flex;align-items:center;gap:.9rem;background:rgba(212,160,23,.22);border:1.5px solid rgba(212,160,23,.65);border-radius:100px;padding:.65rem 2rem;">
                <span style="color:#D4A017;font-weight:800;font-size:.95rem;text-transform:uppercase;letter-spacing:.15em;">Theme</span>
                <span style="width:1px;height:18px;background:rgba(212,160,23,.5);display:inline-block;"></span>
                <span style="color:#fff;font-weight:800;font-size:1.25rem;">Healthy Church</span>
            </span>
        </div>

        {{-- Description --}}
        <p class="hero-fadeup-4" style="color:rgba(255,255,255,.88);font-size:1.2rem;line-height:1.8;max-width:640px;margin:0 auto 2.8rem;">
            A global gathering for pastors &amp; leaders to examine the spiritual, relational and missional
            markers of a healthy church.&nbsp;
            <strong style="color:#fff;">1,500+ leaders from 27 nations.</strong>
        </p>

        {{-- CTAs --}}
        <div class="hero-fadeup-4" style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;margin-bottom:2.8rem;">
            <a href="{{ route('register.start') }}" class="hero-cta-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 12v7a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-7"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                Register Now
            </a>
            {{-- DISABLED: awaiting PayPal approval
            <a href="{{ route('donate') }}" class="hero-cta-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="2.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                <span style="color:#D4A017;">Donate</span>
            </a>
            --}}
        </div>

        {{-- Date & venue --}}
        <div class="hero-fadeup-5" style="display:flex;gap:1.8rem;justify-content:center;flex-wrap:wrap;">
            <span style="display:flex;align-items:center;gap:.6rem;color:rgba(255,255,255,.85);font-size:1.05rem;font-weight:600;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                August 17th–21st, 2026
            </span>
            <span style="color:rgba(255,255,255,.35);">|</span>
            <span style="display:flex;align-items:center;gap:.6rem;color:rgba(255,255,255,.85);font-size:1.05rem;font-weight:600;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Ggaba Community Church, Uganda
            </span>
        </div>
    </div>

    {{-- Slide indicator dots --}}
    <div style="position:absolute;bottom:2.4rem;left:50%;transform:translateX(-50%);display:flex;gap:.5rem;z-index:3;align-items:center;">
        <button id="dot-0" class="hero-dot" style="width:28px;background:#D4A017;" onclick="heroGoTo(0)"></button>
        <button id="dot-1" class="hero-dot" style="width:10px;background:rgba(255,255,255,.35);" onclick="heroGoTo(1)"></button>
        <button id="dot-2" class="hero-dot" style="width:10px;background:rgba(255,255,255,.35);" onclick="heroGoTo(2)"></button>
        <button id="dot-3" class="hero-dot" style="width:10px;background:rgba(255,255,255,.35);" onclick="heroGoTo(3)"></button>
        <button id="dot-4" class="hero-dot" style="width:10px;background:rgba(255,255,255,.35);" onclick="heroGoTo(4)"></button>
        <button id="dot-5" class="hero-dot" style="width:10px;background:rgba(255,255,255,.35);" onclick="heroGoTo(5)"></button>
    </div>

    {{-- Scroll nudge --}}
    <div style="position:absolute;bottom:5rem;left:50%;animation:heroScrollBob 2.2s ease-in-out infinite;z-index:3;display:flex;flex-direction:column;align-items:center;gap:4px;">
        <span style="color:rgba(255,255,255,.38);font-size:.6rem;letter-spacing:.22em;text-transform:uppercase;">Scroll</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.38)" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
    </div>

    {{-- Slideshow + dot sync script --}}
    <script>
    (function(){
        var bgs  = [document.getElementById('hero-bg-0'),document.getElementById('hero-bg-1'),document.getElementById('hero-bg-2'),document.getElementById('hero-bg-3'),document.getElementById('hero-bg-4'),document.getElementById('hero-bg-5')];
        var dots = [document.getElementById('dot-0'),document.getElementById('dot-1'),document.getElementById('dot-2'),document.getElementById('dot-3'),document.getElementById('dot-4'),document.getElementById('dot-5')];
        var cur  = 0, timer;
        function heroGoTo(n){
            bgs[cur].style.opacity='0'; dots[cur].style.width='10px'; dots[cur].style.background='rgba(255,255,255,.35)';
            cur=n;
            bgs[cur].style.opacity='1'; dots[cur].style.width='28px'; dots[cur].style.background='#D4A017';
        }
        window.heroGoTo=heroGoTo;
        function next(){ heroGoTo((cur+1)%bgs.length); }
        timer=setInterval(next,5000);
    })();
    </script>
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
        <h2 class="text-3xl font-extrabold text-summit mt-3">A Taste of the Renewal Summit</h2>
        <p class="text-gray-500 mt-2 text-sm">Moments from previous summits — worship, training, evenings and community.</p>
    </div>

    {{-- Tab filters --}}
    <div class="flex flex-wrap justify-center gap-2 mb-10">
        <button class="gtab active-tab px-5 py-2 rounded-full text-sm font-semibold border transition" data-panel="panel-all">All</button>
        <button class="gtab px-5 py-2 rounded-full text-sm font-semibold border transition" data-panel="panel-worship">🙌 Praise &amp; Worship</button>
        <button class="gtab px-5 py-2 rounded-full text-sm font-semibold border transition" data-panel="panel-training">📚 Training &amp; Sessions</button>
        <button class="gtab px-5 py-2 rounded-full text-sm font-semibold border transition" data-panel="panel-evenings">🌙 Evening Entertainment</button>
        <button class="gtab px-5 py-2 rounded-full text-sm font-semibold border transition" data-panel="panel-community">🤝 Community</button>
    </div>

    @php
    $groups = [
        [
            'id'    => 'worship',
            'label' => '🙌 Praise & Worship',
            'color' => 'bg-purple-700',
            'images'=> [
                ['src' => 'praise1.jpg',   'caption' => 'Praise & Worship'],
                ['src' => 'evening1.jpg',  'caption' => 'Evening Worship'],
                ['src' => 'evening2.jpg',  'caption' => 'Worship Night'],
            ],
        ],
        [
            'id'    => 'training',
            'label' => '📚 Training & Sessions',
            'color' => 'bg-blue-700',
            'images'=> [
                ['src' => 'trainings.jpg',    'caption' => 'Training Sessions'],
                ['src' => 'training2.jpg',    'caption' => 'Workshop'],
                ['src' => 'training3.jpg',    'caption' => 'Breakout Session'],
                ['src' => 'training4.jpg',    'caption' => 'Plenary Session'],
                ['src' => 'pannel_-24.jpg',   'caption' => 'Panel Discussion'],
            ],
        ],
        [
            'id'    => 'evenings',
            'label' => '🌙 Evening Entertainment',
            'color' => 'bg-indigo-800',
            'images'=> [
                ['src' => 'evening1.jpg',        'caption' => 'Evening Entertainment'],
                ['src' => 'evening2.jpg',         'caption' => 'Evening Programme'],
                ['src' => 'performance2.jpg',     'caption' => 'Live Performance'],
                ['src' => 'performance3.jpg',     'caption' => 'Evening Show'],
                ['src' => 'entertainment.jpg',    'caption' => 'Entertainment'],
                ['src' => 'entertainment2.jpg',   'caption' => 'Evening Celebrations'],
            ],
        ],
        [
            'id'    => 'community',
            'label' => '🤝 Community',
            'color' => 'bg-green-700',
            'images'=> [
                ['src' => 'together.jpg',   'caption' => 'Together'],
                ['src' => 'together1.jpg',  'caption' => 'Summit Community'],
                ['src' => 'together2.jpg',  'caption' => 'Nations Together'],
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

{{-- ── REGISTER CTA (Donate panel hidden — awaiting PayPal approval) ─── --}}
<section class="py-20 bg-gray-100">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-summit text-white px-10 py-16 flex flex-col items-center text-center rounded-3xl shadow-2xl">
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
        {{-- DISABLED: Donate panel hidden — awaiting PayPal approval. Restore by uncommenting:
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
        --}}
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
