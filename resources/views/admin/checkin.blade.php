@extends('layouts.admin')
@section('title', 'Check-In Scanner')
@section('page-title', 'Check-In Scanner')

@section('content')

{{-- ── Live Stats ────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-3 gap-5 mb-8">
    <div class="bg-blue-50 rounded-2xl p-6 text-center">
        <div class="text-4xl font-extrabold text-blue-700" id="today-count">{{ $todayCount }}</div>
        <div class="text-sm text-blue-500 font-medium mt-1">Checked In Today</div>
    </div>
    <div class="bg-green-50 rounded-2xl p-6 text-center">
        <div class="text-4xl font-extrabold text-green-700">{{ $totalPaid }}</div>
        <div class="text-sm text-green-500 font-medium mt-1">Total Confirmed</div>
    </div>
    <div class="col-span-2 md:col-span-1 bg-purple-50 rounded-2xl p-6 text-center">
        <div class="text-4xl font-extrabold text-purple-700" id="pct">
            @if($totalPaid > 0) {{ round(($todayCount / $totalPaid) * 100) }}% @else 0% @endif
        </div>
        <div class="text-sm text-purple-500 font-medium mt-1">Attendance Rate</div>
    </div>
</div>

<div class="grid md:grid-cols-2 gap-6">

    {{-- ── QR Scanner ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-summit mb-2">📲 QR Code Scanner</h2>
        <p class="text-sm text-gray-500 mb-5">
            Use a barcode/QR scanner connected to this computer.
            The scanner will auto-submit when it reads the QR code.
        </p>

        <form id="scanner-form" onsubmit="return false;">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Scan or Type QR Token</label>
            <div class="flex gap-2">
                <input type="text" name="token" id="scanner-input"
                       autofocus autocomplete="off"
                       placeholder="Scan QR or type token here..."
                       class="flex-1 border-2 border-yellow-400 rounded-xl px-4 py-3 text-sm font-mono
                              focus:ring-2 focus:ring-yellow-400 outline-none transition">
                <button type="submit"
                        class="bg-summit hover:opacity-80 text-white font-bold px-5 rounded-xl transition">
                    Go
                </button>
            </div>
        </form>

        {{-- QR Reader toggle --}}
        <div class="mt-5">
            <button onclick="toggleCamera()"
                    class="text-sm text-gold hover:underline font-semibold">
                📷 Use Camera to Scan QR
            </button>
            <div id="camera-area" class="hidden mt-3">
                <div id="reader" class="rounded-xl overflow-hidden border-2 border-yellow-400"></div>
                <p class="text-xs text-gray-400 mt-2">Point the camera at the attendee's QR code.</p>
            </div>
        </div>
    </div>

    {{-- ── Manual lookup ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-summit mb-2">🔍 Manual Check-In by Reference</h2>
        <p class="text-sm text-gray-500 mb-5">
            Look up a registration by reference number or phone number.
        </p>
        <form method="GET" action="{{ route('admin.registrations.index') }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Reference / Phone</label>
            <div class="flex gap-2">
                <input type="text" name="search"
                       placeholder="e.g. RS2026-00001 or 0772..."
                       class="flex-1 border border-gray-300 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-yellow-400">
                <button type="submit"
                        class="bg-gold hover:bg-yellow-600 text-white font-bold px-5 rounded-xl transition">
                    Search
                </button>
            </div>
        </form>

        {{-- Shortcut: direct URL check-in --}}
        <div class="mt-5 bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-xs text-yellow-800">
            <p class="font-bold mb-1">💡 Direct Check-In via URL:</p>
            <code class="break-all">{{ url('/admin/checkin/{qr_token}') }}</code>
            <p class="mt-1">Replace <code>{qr_token}</code> with the token from the QR code.</p>
        </div>
    </div>

</div>

{{-- ── Recent Check-Ins ──────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm p-6 mt-6">
    <h2 class="text-lg font-bold text-summit mb-4">Recent Check-Ins Today</h2>
    @php
        $recent = \App\Models\Registration::where('status', 'checked_in')
            ->whereDate('checked_in_at', today())
            ->orderByDesc('checked_in_at')
            ->take(20)
            ->get();
    @endphp

    @if($recent->isEmpty())
        <p class="text-gray-400 text-sm text-center py-6">No check-ins yet today.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-gray-500 text-xs border-b">
                    <tr>
                        <th class="pb-2 pr-4">Time</th>
                        <th class="pb-2 pr-4">Reference</th>
                        <th class="pb-2 pr-4">Name</th>
                        <th class="pb-2 pr-4">Phone</th>
                        <th class="pb-2 pr-4">Type</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recent as $reg)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 pr-4 text-gray-400 text-xs">{{ $reg->checked_in_at->format('H:i:s') }}</td>
                        <td class="py-2 pr-4 font-mono text-xs font-bold text-gold">{{ $reg->reference }}</td>
                        <td class="py-2 pr-4 font-semibold">{{ $reg->full_name }}</td>
                        <td class="py-2 pr-4 text-gray-500">{{ $reg->phone }}</td>
                        <td class="py-2 pr-4">
                            <span class="text-xs px-2 py-0.5 rounded-full font-bold
                                {{ $reg->country_type === 'international' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $reg->country_type }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection

@push('scripts')
{{-- html5-qrcode for camera scanning --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let scanner = null;
    const baseUrl = '{{ url('/admin/checkin') }}/';

    function toggleCamera() {
        const area = document.getElementById('camera-area');
        area.classList.toggle('hidden');
        if (!area.classList.contains('hidden')) {
            startCamera();
        } else {
            stopCamera();
        }
    }

    function startCamera() {
        scanner = new Html5Qrcode("reader");
        scanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            function(decodedText) {
                // Extract token from URL or use raw
                let token = decodedText;
                const verifyMatch = decodedText.match(/\/verify\/([a-f0-9-]+)/);
                if (verifyMatch) token = verifyMatch[1];
                stopCamera();
                window.location.href = baseUrl + token;
            },
            function(err) { /* ignore scan errors */ }
        ).catch(console.error);
    }

    function stopCamera() {
        if (scanner) {
            scanner.stop().then(function() {
                scanner = null;
                document.getElementById('camera-area').classList.add('hidden');
            }).catch(console.error);
        }
    }

    // Redirect directly to /admin/checkin/{token} on form submit
    document.getElementById('scanner-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const token = document.getElementById('scanner-input').value.trim();
        if (token) window.location.href = '{{ url('/admin/checkin') }}/' + encodeURIComponent(token);
    });

    // Auto-redirect after 50ms idle (handles hardware barcode scanners)
    let scanTimer;
    document.getElementById('scanner-input').addEventListener('input', function() {
        clearTimeout(scanTimer);
        scanTimer = setTimeout(function() {
            const token = document.getElementById('scanner-input').value.trim();
            if (token) window.location.href = '{{ url('/admin/checkin') }}/' + encodeURIComponent(token);
        }, 50);
    });

    document.getElementById('scanner-input').focus();
</script>
@endpush
