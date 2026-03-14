<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Renewal Summit 2026</title>
    @vite(['resources/css/app.css'])
</head>
<body style="min-height:100vh; display:flex; align-items:stretch; margin:0; background:#f3f4f6;">

    {{-- ── LEFT PANEL (branding) ──────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-summit flex-col items-center justify-center px-12"
         style="background-color:#0f1f3d;">

        {{-- Background image with dark overlay --}}
        <div class="absolute inset-0">
            <img src="{{ asset('images/praise1.jpg') }}" alt=""
                 class="w-full h-full object-cover opacity-20">
            <div class="absolute inset-0 bg-gradient-to-br from-summit via-summit/90 to-blue-950"></div>
        </div>

        <div class="relative z-10 flex flex-col items-center text-center gap-8">

            <img src="{{ asset('images/summit26.png') }}" alt="Renewal Summit 2026"
                 class="h-40 w-auto object-contain drop-shadow-2xl">

            <div class="border-t border-white/20 w-24"></div>

            <div class="space-y-3">
                <h1 class="text-white text-3xl font-extrabold leading-tight">
                    Renewal Summit<br>International Conference
                </h1>
                <p class="text-blue-200 text-base leading-relaxed max-w-xs">
                    August 17–21, 2026 &nbsp;·&nbsp; Ggaba Community Church, Uganda
                </p>
            </div>

            <div class="grid grid-cols-3 gap-8 text-center mt-4">
                <div>
                    <div class="text-gold text-3xl font-extrabold">1500+</div>
                    <div class="text-blue-300 text-xs uppercase tracking-widest mt-1">Leaders</div>
                </div>
                <div>
                    <div class="text-gold text-3xl font-extrabold">27</div>
                    <div class="text-blue-300 text-xs uppercase tracking-widest mt-1">Nations</div>
                </div>
                <div>
                    <div class="text-gold text-3xl font-extrabold">5</div>
                    <div class="text-blue-300 text-xs uppercase tracking-widest mt-1">Days</div>
                </div>
            </div>

            <p class="text-white/30 text-xs mt-8">
                &copy; {{ date('Y') }} Renewal Summit. All rights reserved.
            </p>
        </div>
    </div>

    {{-- ── RIGHT PANEL (login form) ───────────────────────────── --}}
    <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; background:#f3f4f6; padding:3rem 1.5rem; min-height:100vh;">

        <div style="width:100%; max-width:440px;">
            <div style="background:white; border-radius:1.5rem; box-shadow:0 20px 60px rgba(0,0,0,0.12); padding:2.5rem;">

                {{-- Logo + Header inside card --}}
                <div class="mb-8 text-center">
                    <img src="{{ asset('images/summit26.png') }}" alt="Renewal Summit 2026"
                         class="h-20 w-auto mx-auto object-contain mb-5">
                    <div class="border-t border-gray-100 pt-5">
                        <h2 class="text-2xl font-extrabold text-gray-900">Admin Portal</h2>
                        <p class="text-gray-400 text-sm mt-1">Sign in to manage the Summit</p>
                    </div>
                </div>

                {{-- Errors --}}
                @if ($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @session('status')
                <div class="mb-5 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">
                    {{ $value }}
                </div>
                @endsession

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                        <input id="email" name="email" type="email"
                               value="{{ old('email') }}"
                               required autofocus autocomplete="username"
                               placeholder="username"
                               class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm
                                      text-gray-900 placeholder-gray-400
                                      focus:outline-none focus:ring-2 focus:ring-summit focus:border-summit transition">
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password"
                                   required autocomplete="current-password"
                                   placeholder="password"
                                   class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 pr-12 text-sm
                                          text-gray-900 placeholder-gray-400
                                          focus:outline-none focus:ring-2 focus:ring-summit focus:border-summit transition">
                            <button type="button" onclick="togglePw()"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                             -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input id="remember_me" name="remember" type="checkbox"
                                   class="w-4 h-4 rounded border-gray-300 text-summit focus:ring-summit">
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm text-summit hover:underline font-medium">Forgot password?</a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            style="background-color:#1a2a4a;"
                            onmouseover="this.style.backgroundColor='#1e3a8a'"
                            onmouseout="this.style.backgroundColor='#1a2a4a'"
                            class="w-full text-white font-bold py-3.5 px-6
                                   rounded-xl transition shadow-md hover:shadow-xl text-sm tracking-wide">
                        Sign In to Admin Portal
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-gray-100 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-summit transition">
                        ← Back to Summit website
                    </a>
                </div>

            </div>

            <p class="text-center text-gray-400 text-xs mt-5">
                Renewal Summit 2026 &nbsp;·&nbsp; Authorised personnel only
            </p>
        </div>
    </div>

    <script>
    function togglePw() {
        var pw = document.getElementById('password');
        pw.type = pw.type === 'password' ? 'text' : 'password';
    }
    </script>
</body>
</html>
