<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') – Renewal Summit 2026</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-summit { background-color: #1a1a2e; }
        .gold      { color: #D4A017; }
        .bg-gold   { background-color: #D4A017; }
        .sidebar-link { @apply flex items-center gap-3 px-4 py-2.5 text-sm text-gray-300 rounded-xl hover:bg-white/10 hover:text-white transition; }
        .sidebar-link.active { @apply bg-gold text-white font-semibold; }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- ── Sidebar ────────────────────────────────────────────────── --}}
    <aside class="bg-summit w-64 flex-shrink-0 flex flex-col overflow-y-auto">
        <div class="px-6 py-5 border-b border-white/10">
            <a href="{{ route('admin.dashboard') }}" class="block">
                <span class="text-white font-extrabold text-lg">RENEWAL <span class="gold">SUMMIT</span></span>
                <span class="ml-1 bg-gold text-white text-xs font-bold px-1.5 py-0.5 rounded">2026</span>
            </a>
            <p class="text-xs text-gray-500 mt-0.5">Admin Panel</p>
        </div>

        <nav class="flex-1 px-3 py-5 space-y-1">
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('admin.registrations.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.registrations.*') ? 'active' : '' }}">
                📋 Registrations
            </a>
            <a href="{{ route('admin.checkin') }}"
               class="sidebar-link {{ request()->routeIs('admin.checkin*') ? 'active' : '' }}">
                📲 Check-In Scanner
            </a>
            <div class="border-t border-white/10 my-3"></div>
            <a href="{{ route('home') }}" target="_blank"
               class="sidebar-link">🌐 Public Site ↗</a>
            <a href="{{ route('register.start') }}" target="_blank"
               class="sidebar-link">📝 Register ↗</a>
        </nav>

        <div class="px-4 py-4 border-t border-white/10">
            <p class="text-xs text-gray-500 mb-2">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-xs text-gray-400 hover:text-white transition">Sign Out →</button>
            </form>
        </div>
    </aside>

    {{-- ── Main ────────────────────────────────────────────────────── --}}
    <main class="flex-1 overflow-y-auto">

        {{-- Top bar --}}
        <div class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <h1 class="text-xl font-extrabold text-summit">@yield('page-title', 'Dashboard')</h1>
            <div class="flex items-center gap-4 text-sm text-gray-500">
                <span>📅 {{ now()->format('D d M Y') }}</span>
                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">● LIVE</span>
            </div>
        </div>

        {{-- Flash --}}
        <div class="px-8 pt-4">
            @foreach(['success' => 'green', 'error' => 'red', 'info' => 'blue', 'warning' => 'yellow'] as $type => $color)
                @if(session($type))
                    <div class="bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-800 rounded-xl px-4 py-3 text-sm mb-4">
                        {{ session($type) }}
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Content --}}
        <div class="px-8 py-6">
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')
</body>
</html>
