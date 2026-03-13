<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Renewal Summit 2026')</title>
    <meta name="description" content="Renewal Summit 2026 – Healthy Church. August 17-21, 2026 at Gaba Community Church, Uganda.">

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

    <!-- Navbar -->
    <nav class="bg-summit shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <span class="text-white font-extrabold text-xl tracking-tight">RENEWAL <span class="gold">SUMMIT</span></span>
                    <span class="bg-gold text-white text-xs font-bold px-2 py-0.5 rounded">2026</span>
                </a>
                <div class="hidden md:flex items-center space-x-6 text-sm font-medium">
                    <a href="{{ route('home') }}#about"    class="text-gray-300 hover:text-white transition">About</a>
                    <a href="{{ route('home') }}#speakers" class="text-gray-300 hover:text-white transition">Speakers</a>
                    <a href="{{ route('home') }}#contact"  class="text-gray-300 hover:text-white transition">Contact</a>
                    <a href="{{ route('donate') }}"        class="text-yellow-400 hover:text-yellow-300 transition font-semibold">Donate</a>
                    <a href="{{ route('register.start') }}"
                       class="bg-gold hover:bg-yellow-600 text-white font-bold px-4 py-2 rounded-lg transition shadow">
                        Register Now
                    </a>
                </div>
                <button class="md:hidden text-gray-300 hover:text-white"
                        onclick="document.getElementById('mob-nav').classList.toggle('hidden')">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="mob-nav" class="hidden md:hidden bg-summit border-t border-gray-700 px-4 pb-3 space-y-2">
            <a href="{{ route('home') }}#about"    class="block text-gray-300 py-2">About</a>
            <a href="{{ route('home') }}#speakers" class="block text-gray-300 py-2">Speakers</a>
            <a href="{{ route('home') }}#contact"  class="block text-gray-300 py-2">Contact</a>
            <a href="{{ route('donate') }}"        class="block text-yellow-400 py-2 font-semibold">Donate</a>
            <a href="{{ route('register.start') }}"
               class="block bg-gold text-white text-center font-bold px-4 py-2 rounded-lg mt-2">Register Now</a>
        </div>
    </nav>

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
    <footer class="bg-summit text-gray-400 py-12 mt-16" id="contact">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h4 class="text-white font-bold text-lg mb-3">Renewal Summit 2026</h4>
                    <p class="text-sm leading-relaxed">A global gathering of pastors and leaders to examine
                    the spiritual, relational, and missional markers of a healthy church.</p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-3">Event Details</h4>
                    <ul class="text-sm space-y-1">
                        <li>📅 August 17–21, 2026</li>
                        <li>📍 Gaba Community Church, Uganda</li>
                        <li>🌍 1,500 Leaders · 27 Nations</li>
                        <li>🏷 Theme: Healthy Church</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-3">Quick Links</h4>
                    <ul class="text-sm space-y-1">
                        <li><a href="{{ route('register.start') }}" class="gold hover:text-yellow-300">Register</a></li>
                        <li><a href="{{ route('donate') }}"         class="hover:text-white">Donate</a></li>
                        <li><a href="mailto:info@renewalsummit.ug" class="hover:text-white">info@renewalsummit.ug</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-xs">
                © {{ date('Y') }} Renewal Summit 2026 – Gaba Community Church, Uganda.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
