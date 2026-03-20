<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') – Renewal Summit 2026</title>
    <link rel="icon" type="image/png" href="{{ asset('images/summit26.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/summit26.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; color: #1e293b; }

        /* Layout */
        .admin-wrap  { display: flex; height: 100vh; overflow: hidden; }
        .admin-aside { width: 240px; min-width: 240px; background: #0f1f3d; display: flex; flex-direction: column; overflow-y: auto; transition: transform .28s ease; z-index: 40; }
        .admin-main  { flex: 1; overflow-y: auto; display: flex; flex-direction: column; min-width: 0; }

        /* Mobile overlay */
        .sb-overlay  { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 39; }
        .sb-overlay.show { display: block; }

        /* Hamburger */
        .sb-toggle   { display: none; background: none; border: none; cursor: pointer; padding: 4px 6px; border-radius: 6px; }
        .sb-toggle:hover { background: #f1f5f9; }
        .sb-toggle svg { display: block; }

        @media (max-width: 768px) {
            .admin-aside { position: fixed; top: 0; left: 0; height: 100%; transform: translateX(-260px); }
            .admin-aside.open { transform: translateX(0); box-shadow: 4px 0 24px rgba(0,0,0,.35); }
            .sb-toggle { display: flex; align-items: center; justify-content: center; }
            .admin-content { padding: 16px; }
            .admin-topbar { padding: 12px 16px; }
            .flash-wrap { padding: 12px 16px 0 !important; }
            .topbar-date { display: none; }
        }

        /* Sidebar brand */
        .sb-brand    { padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,.08); }
        .sb-brand-name { color: #fff; font-weight: 800; font-size: 1.05rem; letter-spacing: .01em; }
        .sb-brand-name span { color: #D4A017; }
        .sb-badge    { display: inline-block; background: #D4A017; color: #fff; font-size: .65rem; font-weight: 700; padding: 1px 6px; border-radius: 4px; margin-left: 4px; vertical-align: middle; }
        .sb-sub      { color: #64748b; font-size: .7rem; margin-top: 3px; }

        /* Nav links */
        .sb-nav      { flex: 1; padding: 16px 12px; }
        .sb-link     { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 10px;
                       color: #94a3b8; font-size: .85rem; font-weight: 500; text-decoration: none;
                       transition: background .15s, color .15s; white-space: nowrap; }
        .sb-link:hover  { background: rgba(255,255,255,.08); color: #fff; }
        .sb-link.active { background: #D4A017; color: #fff; font-weight: 700; }
        .sb-link .icon  { font-size: 1.1rem; width: 22px; text-align: center; flex-shrink: 0; }
        .sb-divider  { border: none; border-top: 1px solid rgba(255,255,255,.08); margin: 10px 0; }

        /* Sidebar footer */
        .sb-foot     { padding: 14px 18px; border-top: 1px solid rgba(255,255,255,.08); }
        .sb-user     { color: #64748b; font-size: .75rem; margin-bottom: 6px; }
        .sb-logout   { background: none; border: none; color: #94a3b8; font-size: .8rem; cursor: pointer; transition: color .15s; padding: 0; font-family: inherit; }
        .sb-logout:hover { color: #fff; }

        /* Topbar */
        .admin-topbar { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 14px 32px;
                        display: flex; align-items: center; justify-content: space-between;
                        position: sticky; top: 0; z-index: 10; }
        .admin-topbar h1 { font-size: 1.2rem; font-weight: 800; color: #0f1f3d; }
        .topbar-meta { display: flex; align-items: center; gap: 12px; font-size: .8rem; color: #64748b; }
        .live-badge  { background: #dcfce7; color: #166534; font-size: .7rem; font-weight: 700;
                       padding: 2px 10px; border-radius: 999px; }

        /* Content */
        .admin-content { padding: 28px 32px; flex: 1; }

        /* Flash alerts */
        .flash-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; border-radius:10px; padding:10px 16px; font-size:.85rem; margin-bottom:12px; }
        .flash-error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px; padding:10px 16px; font-size:.85rem; margin-bottom:12px; }
        .flash-info    { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; border-radius:10px; padding:10px 16px; font-size:.85rem; margin-bottom:12px; }
        .flash-warning { background:#fffbeb; border:1px solid #fde68a; color:#92400e; border-radius:10px; padding:10px 16px; font-size:.85rem; margin-bottom:12px; }
    </style>
</head>
<body>

<div class="admin-wrap">

    {{-- Mobile sidebar overlay --}}
    <div class="sb-overlay" id="sb-overlay" onclick="closeSidebar()"></div>

    {{-- ── Sidebar ──────────────────────────────────────────── --}}
    <aside class="admin-aside" id="admin-aside">

        {{-- Brand --}}
        <div class="sb-brand">
            <a href="{{ route('admin.dashboard') }}" style="text-decoration:none; display:block;">
                <img src="{{ asset('images/summit26.png') }}" alt="Renewal Summit 2026"
                     style="height:52px; width:auto; object-fit:contain; display:block;">
            </a>
            <div class="sb-sub" style="margin-top:6px;">Admin Panel</div>
        </div>

        {{-- Navigation --}}
        <nav class="sb-nav">
            <a href="{{ route('admin.dashboard') }}"
               class="sb-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span> Dashboard
            </a>
            <a href="{{ route('admin.registrations.index') }}"
               class="sb-link {{ request()->routeIs('admin.registrations.*') ? 'active' : '' }}">
                <span class="icon">📋</span> Registrations
            </a>
            <a href="{{ route('admin.checkin') }}"
               class="sb-link {{ request()->routeIs('admin.checkin*') ? 'active' : '' }}">
                <span class="icon">📲</span> Check-In Scanner
            </a>
            <a href="{{ route('admin.testimonials.index') }}"
               class="sb-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}">
                <span class="icon">🎥</span> Video Testimonials
            </a>
            <a href="{{ route('admin.hotels.index') }}"
               class="sb-link {{ request()->routeIs('admin.hotels.*') ? 'active' : '' }}">
                <span class="icon">🏨</span> Hotels & Rates
            </a>

            <hr class="sb-divider">

            <a href="{{ route('home') }}" target="_blank" class="sb-link">
                <span class="icon">🌐</span> Public Site ↗
            </a>
           
        </nav>

        {{-- Footer --}}
        <div class="sb-foot">
            <div class="sb-user">{{ auth()->user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sb-logout">Sign Out →</button>
            </form>
        </div>

    </aside>

    {{-- ── Main ────────────────────────────────────────────────── --}}
    <main class="admin-main">

        {{-- Top bar --}}
        <div class="admin-topbar">
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="sb-toggle" id="sb-toggle" onclick="openSidebar()" title="Menu">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0f1f3d" stroke-width="2.2">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="topbar-meta">
                <span class="topbar-date">📅 {{ now()->format('D d M Y') }}</span>
                <span class="live-badge">● LIVE</span>
            </div>
        </div>

        {{-- Flash messages --}}
        <div style="padding: 16px 32px 0;" class="flash-wrap">
            @if(session('success'))
                <div class="flash-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash-error">{{ session('error') }}</div>
            @endif
            @if(session('info'))
                <div class="flash-info">{{ session('info') }}</div>
            @endif
            @if(session('warning'))
                <div class="flash-warning">{{ session('warning') }}</div>
            @endif
        </div>

        {{-- Page content --}}
        <div class="admin-content">
            @yield('content')
        </div>

    </main>
</div>

@stack('scripts')
<script>
function openSidebar(){
    document.getElementById('admin-aside').classList.add('open');
    document.getElementById('sb-overlay').classList.add('show');
}
function closeSidebar(){
    document.getElementById('admin-aside').classList.remove('open');
    document.getElementById('sb-overlay').classList.remove('show');
}
// Close sidebar on nav link click (mobile)
document.querySelectorAll('.sb-link').forEach(function(el){
    el.addEventListener('click', function(){ if(window.innerWidth<=768) closeSidebar(); });
});
</script>
</body>
</html>
