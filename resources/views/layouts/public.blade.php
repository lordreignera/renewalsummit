<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Renewal Summit 2026')</title>
    <meta name="description" content="Renewal Summit 2026 – Healthy Church. August 17-21, 2026 at Gaba Community Church, Uganda.">
    <link rel="icon" type="image/png" href="{{ asset('images/summit26.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/summit26.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .gold         { color: #D4A017; }
        .bg-gold      { background-color: #D4A017; }
        .border-gold  { border-color: #D4A017; }
        .text-summit  { color: #1a1a2e; }
        .bg-summit    { background-color: #1a1a2e; }
        .hover\:bg-gold-dark:hover { background-color: #b8880f; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    @php $embedded = request()->boolean('embed'); @endphp

    <!-- Navbar -->
    @if(! $embedded)
    <nav style="background:#ffffff; box-shadow:0 2px 8px rgba(0,0,0,0.10); position:sticky; top:0; z-index:50;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between" style="height:68px;">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/summit26.png') }}" alt="Renewal Summit 2026"
                         style="height:56px; width:auto; object-fit:contain;">
                </a>
                <div class="hidden md:flex items-center" style="gap:1.5rem; font-size:0.9rem; font-weight:600;">
                    <a href="{{ route('home') }}#about"    style="color:#1a2a4a;" onmouseover="this.style.color='#D4A017'" onmouseout="this.style.color='#1a2a4a'">About</a>
                    <a href="{{ route('home') }}#gallery"  style="color:#1a2a4a;" onmouseover="this.style.color='#D4A017'" onmouseout="this.style.color='#1a2a4a'">Gallery</a>
                    <a href="{{ route('home') }}#speakers" style="color:#1a2a4a;" onmouseover="this.style.color='#D4A017'" onmouseout="this.style.color='#1a2a4a'">Speakers</a>
                    <a href="{{ route('home') }}#contact"  style="color:#1a2a4a;" onmouseover="this.style.color='#D4A017'" onmouseout="this.style.color='#1a2a4a'">Contact</a>
                    <a href="{{ route('home') }}#accommodation" style="color:#1a2a4a;" onmouseover="this.style.color='#D4A017'" onmouseout="this.style.color='#1a2a4a'">Accommodation</a>
                    {{-- DISABLED: awaiting PayPal approval --}}
                    {{-- <a href="{{ route('donate') }}"        style="color:#D4A017; font-weight:700;" onmouseover="this.style.color='#b38610'" onmouseout="this.style.color='#D4A017'">Donate</a> --}}
                    <a href="{{ route('register.start') }}"
                       style="background:#D4A017; color:#fff; font-weight:700; padding:0.5rem 1.1rem; border-radius:0.5rem; text-decoration:none;"
                       onmouseover="this.style.background='#0f1f3d'" onmouseout="this.style.background='#D4A017'">
                        Register Now
                    </a>
                </div>
                <button class="md:hidden" style="color:#1a2a4a; background:none; border:none; cursor:pointer;"
                        onclick="document.getElementById('mob-nav').classList.toggle('hidden')">
                    <svg style="height:1.6rem;width:1.6rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="mob-nav" class="hidden md:hidden" style="background:#fff; border-top:1px solid #e5e7eb; padding:0.5rem 1rem 1rem;">
            <a href="{{ route('home') }}#about"    style="display:block; color:#1a2a4a; padding:0.5rem 0; font-weight:600;">About</a>
            <a href="{{ route('home') }}#gallery"  style="display:block; color:#1a2a4a; padding:0.5rem 0; font-weight:600;">Gallery</a>
            <a href="{{ route('home') }}#speakers" style="display:block; color:#1a2a4a; padding:0.5rem 0; font-weight:600;">Speakers</a>
            <a href="{{ route('home') }}#contact"  style="display:block; color:#1a2a4a; padding:0.5rem 0; font-weight:600;">Contact</a>
            <a href="{{ route('home') }}#accommodation" style="display:block; color:#1a2a4a; padding:0.5rem 0; font-weight:600;">Accommodation</a>
            {{-- DISABLED: awaiting PayPal approval --}}
            {{-- <a href="{{ route('donate') }}"        style="display:block; color:#D4A017; padding:0.5rem 0; font-weight:700;">Donate</a> --}}
            <a href="{{ route('register.start') }}"
               style="display:block; background:#D4A017; color:#fff; text-align:center; font-weight:700; padding:0.6rem 1rem; border-radius:0.5rem; margin-top:0.5rem; text-decoration:none;">Register Now</a>
        </div>
    </nav>
    @endif

    <!-- Flash Messages -->
    @foreach(['success' => 'green', 'error' => 'red', 'info' => 'blue', 'warning' => 'yellow'] as $type => $color)
        @if(session($type))
            <div class="bg-{{ $color }}-50 border-l-4 border-{{ $color }}-500 text-{{ $color }}-800 px-6 py-3 text-sm">
                {{ session($type) }}
            </div>
        @endif
    @endforeach

    <!-- Content -->
    @yield('content')

    <!-- Footer -->
    @if(! $embedded)
    <footer id="contact" style="position:relative;overflow:hidden;color:#fff;">

        {{-- Background photo --}}
        <div style="position:absolute;inset:0;background-image:url('{{ asset('images/holycommunion.jpg') }}');background-size:cover;background-position:center top;z-index:0;"></div>
        {{-- Dark overlay matching hero style --}}
        <div style="position:absolute;inset:0;background:linear-gradient(to bottom, rgba(10,22,52,.82) 0%, rgba(10,22,52,.88) 60%, rgba(10,22,52,.97) 100%);z-index:1;"></div>

        <div style="position:relative;z-index:2;max-width:1200px;margin:0 auto;padding:4.5rem 1.5rem 0;">

            {{-- Logo centred with glow --}}
            <div style="text-align:center;margin-bottom:2.8rem;">
                <img src="{{ asset('images/summit26.png') }}" alt="Renewal Summit 2026"
                     style="height:160px;width:auto;object-fit:contain;display:inline-block;
                            filter:drop-shadow(0 0 36px rgba(255,255,255,.9)) drop-shadow(0 0 16px rgba(212,160,23,.75)) drop-shadow(0 6px 28px rgba(0,0,0,.9));">
            </div>

            {{-- Tagline --}}
            <p style="text-align:center;color:rgba(255,255,255,.65);font-size:1rem;max-width:520px;margin:0 auto 3.5rem;line-height:1.7;">
                A global gathering of pastors &amp; leaders to examine the spiritual,
                relational, and missional markers of a healthy church.
            </p>

            {{-- Three columns --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:3rem;margin-bottom:3.5rem;">

                <div>
                    <h4 style="color:#D4A017;font-weight:700;font-size:.72rem;text-transform:uppercase;letter-spacing:.22em;margin-bottom:1.2rem;">Event Details</h4>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.75rem;">
                        <li style="display:flex;align-items:center;gap:.6rem;color:rgba(255,255,255,.75);font-size:.92rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            August 17th–21st, 2026
                        </li>
                        <li style="display:flex;align-items:center;gap:.6rem;color:rgba(255,255,255,.75);font-size:.92rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Ggaba Community Church, Uganda
                        </li>
                            <li style="display:flex;align-items:center;gap:.6rem;color:rgba(255,255,255,.75);font-size:.92rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            1,500 Leaders · 30+ Nations
                        </li>
                        <li style="display:flex;align-items:center;gap:.6rem;color:rgba(255,255,255,.75);font-size:.92rem;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#D4A017" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                            Theme: Healthy Church
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 style="color:#D4A017;font-weight:700;font-size:.72rem;text-transform:uppercase;letter-spacing:.22em;margin-bottom:1.2rem;">Quick Links</h4>
                    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:.75rem;">
                        <li><a href="{{ route('register.start') }}" style="color:#D4A017;text-decoration:none;font-size:.92rem;font-weight:600;" onmouseover="this.style.color='#ffe082'" onmouseout="this.style.color='#D4A017'">→ Register Now</a></li>
                        <li><a href="https://afru.ac.ug/" target="_blank" rel="noopener" style="color:rgba(255,255,255,.75);text-decoration:none;font-size:.85rem;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.75)'">→ afru.ac.ug</a></li>
                        <li><a href="https://renewalhealthcare.org/" target="_blank" rel="noopener" style="color:rgba(255,255,255,.75);text-decoration:none;font-size:.85rem;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.75)'">→ renewalhealthcare.org</a></li>
                        <li><a href="https://africarenewal.org/" target="_blank" rel="noopener" style="color:rgba(255,255,255,.75);text-decoration:none;font-size:.85rem;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.75)'">→ africarenewal.org</a></li>
                        <li><a href="https://gabachurch.org/" target="_blank" rel="noopener" style="color:rgba(255,255,255,.75);text-decoration:none;font-size:.85rem;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.75)'">→ gabachurch.org</a></li>
                    </ul>
                </div>

                <div>
                    <h4 style="color:#D4A017;font-weight:700;font-size:.72rem;text-transform:uppercase;letter-spacing:.22em;margin-bottom:1.2rem;">Hosted &amp; Supported By</h4>
                    <div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap;">
                        <div style="background:#fff;border-radius:1rem;padding:.7rem;width:100px;height:100px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 18px rgba(0,0,0,.5);">
                            <img src="{{ asset('images/gabba_log.jpeg') }}" alt="Gaba Community Church" style="width:100%;height:100%;object-fit:contain;">
                        </div>
                        <div style="background:#fff;border-radius:1rem;padding:.7rem;width:100px;height:100px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 18px rgba(0,0,0,.5);">
                            <img src="{{ asset('images/fellowship_log.jpeg') }}" alt="Fellowship of Christian Churches" style="width:100%;height:100%;object-fit:contain;">
                        </div>
                        <div style="background:#fff;border-radius:1rem;padding:.7rem;width:100px;height:100px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 18px rgba(0,0,0,.5);">
                            <img src="{{ asset('images/maranatha_log.jpeg') }}" alt="Maranatha Schools" style="width:100%;height:100%;object-fit:contain;">
                        </div>
                    </div>
                </div>

            </div>

            {{-- Bottom bar --}}
            <div style="border-top:1px solid rgba(255,255,255,.12);padding:1.4rem 0;text-align:center;">
                <div style="color:rgba(255,255,255,.75);font-size:.92rem;margin-bottom:.6rem;">
                    RSVP: <a href="mailto:renewalsummit@africarenewal.org" style="color:#D4A017;text-decoration:none;">renewalsummit@africarenewal.org</a>
                    &nbsp;&middot;&nbsp;
                    Phone: <a href="tel:+256772120855" style="color:#D4A017;text-decoration:none;">+256772120855</a>
                </div>
                <p style="color:rgba(255,255,255,.35);font-size:.8rem;">
                    © {{ date('Y') }} Renewal Summit 2026 – Ggaba Community Church, Uganda.
                </p>
            </div>
        </div>
    </footer>
    @endif

    @stack('scripts')
</body>
</html>
