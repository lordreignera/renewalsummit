@extends('layouts.admin')
@section('title', 'Check-In Scanner')
@section('page-title', 'Check-In Scanner')

@php use Illuminate\Support\Facades\Storage; @endphp

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
            Scan the attendee's QR with the camera below, or type / paste the reference number
            (e.g. <strong>RS-2026-00001</strong>). The scanned QR text is parsed automatically.
        </p>

        <form id="scanner-form" onsubmit="return false;">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Scan QR or Type Reference</label>
            <div class="flex gap-2">
                <input type="text" name="token" id="scanner-input"
                       autofocus autocomplete="off"
                       placeholder="e.g. RS-2026-00001 or paste QR text..."
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
        <h2 class="text-lg font-bold text-summit mb-2">🔍 Search &amp; Find Attendee</h2>
        <p class="text-sm text-gray-500 mb-5">
            Search by name, phone, email or reference to find the attendee below, then click
            <strong>Check In</strong> on their row.
        </p>
        <form method="GET" action="{{ route('admin.checkin') }}">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Name / Phone / Reference</label>
            <div class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="e.g. John, 0772…, RS-2026-00001"
                       class="flex-1 border border-gray-300 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-yellow-400">
                <button type="submit"
                        class="bg-gold hover:bg-yellow-600 text-white font-bold px-5 rounded-xl transition">
                    Search
                </button>
            </div>
        </form>

        {{-- Hint --}}
        <div class="mt-5 bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-xs text-yellow-800">
            <p class="font-bold mb-1">💡 Direct Check-In via URL:</p>
            <code class="break-all">{{ url('/admin/checkin/RS-2026-00001') }}</code>
            <p class="mt-1">Replace the reference with the attendee's actual reference number. Works with scanned QR text too.</p>
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

{{-- ── All Paid Attendees List ───────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm p-6 mt-6">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:18px;">
        <h2 class="text-lg font-bold text-summit">🎫 Confirmed Attendees ({{ $attendees->total() }})</h2>
        <form method="GET" action="{{ route('admin.checkin') }}" style="display:flex;gap:8px;flex-wrap:wrap;">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search name, phone, ref..."
                   style="border:1px solid #d1d5db;border-radius:10px;padding:7px 14px;font-size:13px;outline:none;min-width:200px;">
            <select name="origin" style="border:1px solid #d1d5db;border-radius:10px;padding:7px 12px;font-size:13px;outline:none;">
                <option value="">All Origins</option>
                <option value="local"         {{ request('origin') === 'local'         ? 'selected' : '' }}>Uganda</option>
                <option value="africa"        {{ request('origin') === 'africa'        ? 'selected' : '' }}>Rest of Africa</option>
                <option value="international" {{ request('origin') === 'international' ? 'selected' : '' }}>International</option>
            </select>
            <button type="submit" style="background:#1a2a4a;color:#f5c518;padding:7px 18px;border-radius:10px;font-weight:700;font-size:13px;border:none;cursor:pointer;">Filter</button>
            @if(request('search') || request('origin'))
                <a href="{{ route('admin.checkin') }}" style="background:#e5e7eb;color:#374151;padding:7px 14px;border-radius:10px;font-weight:700;font-size:13px;text-decoration:none;">Clear</a>
            @endif
        </form>
    </div>

    @if($attendees->isEmpty())
        <p class="text-gray-400 text-sm text-center py-8">No confirmed attendees found.</p>
    @else
        <div class="overflow-x-auto">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                        <th style="padding:10px 12px;text-align:left;color:#64748b;font-weight:600;white-space:nowrap;">#</th>
                        <th style="padding:10px 12px;text-align:left;color:#64748b;font-weight:600;">Reference</th>
                        <th style="padding:10px 12px;text-align:left;color:#64748b;font-weight:600;">Name</th>
                        <th style="padding:10px 12px;text-align:left;color:#64748b;font-weight:600;">Designation</th>
                        <th style="padding:10px 12px;text-align:left;color:#64748b;font-weight:600;">Origin</th>
                        <th style="padding:10px 12px;text-align:left;color:#64748b;font-weight:600;">Status</th>
                        <th style="padding:10px 12px;text-align:center;color:#64748b;font-weight:600;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendees as $i => $att)
                    @php
                        $originMap = [
                            'local'         => ['label' => 'Uganda',          'bg' => '#dcfce7', 'color' => '#166534'],
                            'africa'        => ['label' => 'Rest of Africa',  'bg' => '#fef9c3', 'color' => '#854d0e'],
                            'international' => ['label' => 'International',   'bg' => '#dbeafe', 'color' => '#1d4ed8'],
                        ];
                        $origin = $originMap[$att->country_type] ?? ['label' => ucfirst($att->country_type ?? '—'), 'bg' => '#f3f4f6', 'color' => '#374151'];
                        $desig  = $att->designation . ($att->designation_specify ? " – {$att->designation_specify}" : '');
                        $qrUrl  = $att->qr_code_path ? Storage::disk('public')->url($att->qr_code_path) : null;
                    @endphp
                    <tr style="border-bottom:1px solid #f1f5f9;{{ $loop->even ? 'background:#fafafa;' : '' }}" class="hover:bg-blue-50">
                        <td style="padding:10px 12px;color:#94a3b8;">{{ $attendees->firstItem() + $loop->index }}</td>
                        <td style="padding:10px 12px;font-family:monospace;font-weight:700;color:#1a2a4a;white-space:nowrap;">{{ $att->reference }}</td>
                        <td style="padding:10px 12px;font-weight:600;">{{ $att->full_name }}</td>
                        <td style="padding:10px 12px;color:#475569;">{{ $desig ?: '—' }}</td>
                        <td style="padding:10px 12px;">
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $origin['bg'] }};color:{{ $origin['color'] }};">
                                {{ $origin['label'] }}
                            </span>
                        </td>
                        <td style="padding:10px 12px;">
                            @if($att->status === 'checked_in')
                                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#e0f2fe;color:#0369a1;">✓ Checked In</span>
                            @else
                                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#dcfce7;color:#166534;">Paid</span>
                            @endif
                        </td>
                        <td style="padding:10px 12px;text-align:center;">
                            @php
                                $attJson = json_encode([
                                    'id'          => $att->id,
                                    'name'        => $att->full_name,
                                    'reference'   => $att->reference,
                                    'phone'       => $att->phone,
                                    'email'       => $att->email,
                                    'designation' => $desig,
                                    'origin'      => $att->country_type,
                                    'status'      => $att->status,
                                    'token'       => $att->qr_token,
                                    'qr_url'      => $qrUrl,
                                    'checkin_url' => route('admin.checkin.confirm', $att->id),
                                    'csrf'        => csrf_token(),
                                ], JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                            @endphp
                            @if($att->status === 'checked_in')
                                <span style="background:#e0f2fe;color:#0369a1;padding:6px 14px;border-radius:8px;font-size:11px;font-weight:700;white-space:nowrap;">✓ Checked In</span>
                            @else
                                <button onclick="openModal(JSON.parse(this.dataset.att))"
                                        data-att='{!! $attJson !!}'
                                        style="background:#16a34a;color:#fff;padding:6px 16px;border-radius:8px;font-size:12px;font-weight:700;border:none;cursor:pointer;white-space:nowrap;">
                                    📲 Check In
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="margin-top:18px;">
            {{ $attendees->links() }}
        </div>
    @endif
</div>

{{-- ── Check-In Verification Modal ───────────────────────────────── --}}
<div id="attendee-modal"
     style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.6);overflow-y:auto;">
    <div style="max-width:560px;margin:40px auto 40px;background:#fff;border-radius:18px;box-shadow:0 8px 40px rgba(0,0,0,0.3);overflow:hidden;">

        {{-- Header --}}
        <div style="background:#1a2a4a;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="color:#f5c518;font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;">Check-In Verification</div>
                <div id="modal-name" style="color:#fff;font-size:19px;font-weight:800;margin-top:3px;"></div>
            </div>
            <button onclick="closeModal()" style="color:#f5c518;font-size:28px;font-weight:700;background:none;border:none;cursor:pointer;line-height:1;">&times;</button>
        </div>

        {{-- Two-column: QR preview + details --}}
        <div style="display:flex;gap:0;border-bottom:1px solid #f0f0f0;">
            {{-- QR they should present --}}
            <div style="width:160px;flex-shrink:0;padding:20px 16px;background:#fafafa;border-right:1px solid #f0f0f0;text-align:center;">
                <div style="font-size:10px;font-weight:700;color:#888;letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;">Their QR</div>
                <img id="modal-qr" src="" alt="QR Code"
                     style="display:inline-block;width:120px;height:120px;border:3px solid #f5c518;border-radius:10px;object-fit:contain;background:#fff;">
                <div id="modal-qr-missing" style="display:none;width:120px;height:120px;border:2px dashed #ccc;border-radius:10px;align-items:center;justify-content:center;margin:0 auto;color:#bbb;font-size:11px;">
                    No QR
                </div>
            </div>
            {{-- Details --}}
            <div style="flex:1;padding:16px 20px;">
                <table style="width:100%;font-size:13px;border-collapse:collapse;">
                    <tr style="border-bottom:1px solid #f5f5f5;">
                        <td style="padding:7px 0;color:#888;font-weight:600;width:110px;">Reference</td>
                        <td id="modal-reference" style="padding:7px 0;font-family:monospace;font-weight:700;color:#1a2a4a;"></td>
                    </tr>
                    <tr style="border-bottom:1px solid #f5f5f5;">
                        <td style="padding:7px 0;color:#888;font-weight:600;">Origin</td>
                        <td style="padding:7px 0;"><span id="modal-origin" style="padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;"></span></td>
                    </tr>
                    <tr style="border-bottom:1px solid #f5f5f5;">
                        <td style="padding:7px 0;color:#888;font-weight:600;">Designation</td>
                        <td id="modal-designation" style="padding:7px 0;font-size:12px;"></td>
                    </tr>
                    <tr style="border-bottom:1px solid #f5f5f5;">
                        <td style="padding:7px 0;color:#888;font-weight:600;">Phone</td>
                        <td id="modal-phone" style="padding:7px 0;"></td>
                    </tr>
                    <tr>
                        <td style="padding:7px 0;color:#888;font-weight:600;">Status</td>
                        <td style="padding:7px 0;"><span id="modal-status" style="padding:2px 10px;border-radius:20px;font-size:11px;font-weight:700;"></span></td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- ── QR Scan Verification Panel ── --}}
        <div style="padding:20px 24px;border-bottom:1px solid #f0f0f0;">
            <div style="font-size:13px;font-weight:700;color:#1a2a4a;margin-bottom:4px;">📲 Scan Attendee's QR Code to Verify</div>
            <div style="font-size:12px;color:#64748b;margin-bottom:12px;">Ask the attendee to show their QR code, then scan it below. The reference must match before check-in is allowed.</div>

            {{-- Scan input (USB scanner or typing) --}}
            <div style="display:flex;gap:8px;margin-bottom:10px;">
                <input type="text" id="modal-scan-input"
                       autocomplete="off" placeholder="Scan QR code here or type reference..."
                       oninput="onModalScanInput(this.value)"
                       style="flex:1;border:2px solid #f5c518;border-radius:10px;padding:9px 14px;font-size:13px;font-family:monospace;outline:none;">
                <button onclick="toggleModalCamera()"
                        style="background:#1a2a4a;color:#f5c518;padding:9px 14px;border-radius:10px;font-size:12px;font-weight:700;border:none;cursor:pointer;white-space:nowrap;"
                        id="modal-cam-btn">📷 Camera</button>
            </div>

            {{-- Camera viewer --}}
            <div id="modal-camera-area" style="display:none;margin-bottom:10px;">
                <div id="modal-reader" style="border-radius:10px;overflow:hidden;border:2px solid #f5c518;"></div>
                <p style="font-size:11px;color:#94a3b8;margin-top:6px;">Point the camera at the attendee's QR code.</p>
            </div>

            {{-- Verification result --}}
            <div id="modal-verify-result" style="display:none;border-radius:10px;padding:12px 16px;font-size:13px;font-weight:600;"></div>
        </div>

        {{-- Hidden POST form (submitted automatically on successful scan) --}}
        <form id="modal-checkin-form" method="POST" action="" style="display:none;">
            <input type="hidden" name="_token" id="modal-csrf">
        </form>

        {{-- Footer --}}
        <div style="padding:14px 24px;background:#f9f9f9;display:flex;gap:10px;justify-content:flex-end;align-items:center;">
            <span style="font-size:12px;color:#94a3b8;flex:1;">Scan must match to enable check-in.</span>
            <button onclick="closeModal()"
                    style="background:#e5e7eb;color:#374151;padding:8px 20px;border-radius:10px;font-weight:700;font-size:13px;border:none;cursor:pointer;">
                Cancel
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- html5-qrcode for camera scanning --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let scanner = null;

    /**
     * Extract a lookup value from raw QR content.
     * - Multi-line text with "Ref: RS-2026-XXXXX"  → returns the reference
     * - Bare reference like "RS-2026-00001"         → returns as-is
     * - Old URL "/verify/{token}"                   → returns the token
     * - Anything else                               → returns the raw trimmed text
     */
    function extractLookupValue(text) {
        text = text.trim();
        // New QR format: contains "Ref: RS-2026-XXXXX" on a line
        const refMatch = text.match(/Ref:\s*(RS-[\w-]+)/i);
        if (refMatch) return refMatch[1];
        // Bare reference number
        if (/^RS-[\w-]+$/i.test(text)) return text;
        // Old URL token
        const verifyMatch = text.match(/\/verify\/([a-f0-9-]{36})/);
        if (verifyMatch) return verifyMatch[1];
        return text;
    }

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
                const lookup = extractLookupValue(decodedText);
                stopCamera();
                window.location.href = '{{ url("/admin/checkin") }}/' + encodeURIComponent(lookup);
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

    // Manual "Go" submit
    document.getElementById('scanner-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const raw = document.getElementById('scanner-input').value.trim();
        if (raw) {
            const lookup = extractLookupValue(raw);
            window.location.href = '{{ url('/admin/checkin') }}/' + encodeURIComponent(lookup);
        }
    });

    // Auto-submit when a scanner device fires input quickly (USB barcode/QR scanners)
    let scanTimer;
    document.getElementById('scanner-input').addEventListener('input', function() {
        clearTimeout(scanTimer);
        const val = this.value;
        scanTimer = setTimeout(function() {
            const raw = val.trim();
            if (raw) {
                const lookup = extractLookupValue(raw);
                window.location.href = '{{ url('/admin/checkin') }}/' + encodeURIComponent(lookup);
            }
        }, 80);
    });

    document.getElementById('scanner-input').focus();

    /* ══════════════════════════════════════════
       ATTENDEE MODAL + QR VERIFICATION
    ══════════════════════════════════════════ */
    let currentAttendee  = null;  // data for the currently open modal
    let modalScanner     = null;  // Html5Qrcode instance inside the modal
    let modalScanTimer   = null;  // debounce timer for typed input

    const originLabels = {
        'local':         { label: 'Uganda', bg: '#dcfce7', color: '#166534' },
        'africa':        { label: 'Rest of Africa', bg: '#fef9c3', color: '#854d0e' },
        'international': { label: 'International', bg: '#dbeafe', color: '#1d4ed8' },
    };

    /* ── Open Modal ── */
    function openModal(data) {
        currentAttendee = data;

        document.getElementById('modal-name').textContent        = data.name;
        document.getElementById('modal-reference').textContent   = data.reference;
        document.getElementById('modal-phone').textContent       = data.phone || '—';
        document.getElementById('modal-designation').textContent = data.designation || '—';

        // Origin badge
        const originEl   = document.getElementById('modal-origin');
        const originInfo = originLabels[data.origin] || { label: data.origin, bg: '#f3f4f6', color: '#374151' };
        originEl.textContent      = originInfo.label;
        originEl.style.background = originInfo.bg;
        originEl.style.color      = originInfo.color;

        // Status badge
        const statusEl = document.getElementById('modal-status');
        statusEl.textContent      = data.status === 'checked_in' ? '✓ Checked In' : 'Paid – Awaiting Check-In';
        statusEl.style.background = data.status === 'checked_in' ? '#e0f2fe' : '#fef9c3';
        statusEl.style.color      = data.status === 'checked_in' ? '#0369a1' : '#854d0e';

        // QR preview image
        const qrImg     = document.getElementById('modal-qr');
        const qrMissing = document.getElementById('modal-qr-missing');
        if (data.qr_url) {
            qrImg.src                   = data.qr_url;
            qrImg.style.display         = 'inline-block';
            qrMissing.style.display     = 'none';
        } else {
            qrImg.style.display         = 'none';
            qrMissing.style.display     = 'flex';
        }

        // Wire up POST form
        document.getElementById('modal-checkin-form').action = data.checkin_url;
        document.getElementById('modal-csrf').value          = data.csrf;

        // Reset scan panel
        document.getElementById('modal-scan-input').value    = '';
        setVerifyResult('idle', '');
        stopModalCamera();
        document.getElementById('modal-camera-area').style.display = 'none';

        document.getElementById('attendee-modal').style.display = 'block';
        document.body.style.overflow = 'hidden';

        // Auto-focus the scan input
        setTimeout(() => document.getElementById('modal-scan-input').focus(), 200);
    }

    /* ── Close Modal ── */
    function closeModal() {
        stopModalCamera();
        document.getElementById('attendee-modal').style.display = 'none';
        document.body.style.overflow = '';
        currentAttendee = null;
    }

    // Close on backdrop click
    document.getElementById('attendee-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    /* ── Verification result display ── */
    function setVerifyResult(state, message) {
        const el = document.getElementById('modal-verify-result');
        if (state === 'idle' || !message) {
            el.style.display = 'none';
            return;
        }
        el.style.display = 'block';
        if (state === 'success') {
            el.style.background = '#dcfce7';
            el.style.color      = '#166534';
            el.style.border     = '1.5px solid #bbf7d0';
        } else {
            el.style.background = '#fee2e2';
            el.style.color      = '#991b1b';
            el.style.border     = '1.5px solid #fecaca';
        }
        el.textContent = message;
    }

    /* ── Core verify-and-check-in logic ── */
    function verifyAndCheckIn(scannedText) {
        if (!currentAttendee) return;
        const lookup = extractLookupValue(scannedText);

        if (lookup.toUpperCase() === currentAttendee.reference.toUpperCase()) {
            setVerifyResult('success', '✅ QR verified! Checking in ' + currentAttendee.name + '…');
            stopModalCamera();
            // Brief pause so the green flash is visible, then submit
            setTimeout(() => document.getElementById('modal-checkin-form').submit(), 700);
        } else {
            setVerifyResult('error',
                '❌ QR does not match this attendee.\n' +
                'Scanned: ' + lookup + ' — Expected: ' + currentAttendee.reference);
        }
    }

    /* ── Typed / USB-scanner input inside modal ── */
    function onModalScanInput(value) {
        clearTimeout(modalScanTimer);
        modalScanTimer = setTimeout(() => {
            const raw = value.trim();
            if (raw) verifyAndCheckIn(raw);
        }, 80);
    }

    /* ── Camera inside modal ── */
    function toggleModalCamera() {
        const area = document.getElementById('modal-camera-area');
        if (area.style.display === 'none') {
            area.style.display = 'block';
            startModalCamera();
            document.getElementById('modal-cam-btn').textContent = '⏹ Stop';
        } else {
            area.style.display = 'none';
            stopModalCamera();
            document.getElementById('modal-cam-btn').textContent = '📷 Camera';
        }
    }

    function startModalCamera() {
        modalScanner = new Html5Qrcode('modal-reader');
        modalScanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 220, height: 220 } },
            function(decodedText) {
                // Stop camera immediately so it doesn't re-fire
                stopModalCamera();
                document.getElementById('modal-camera-area').style.display = 'none';
                document.getElementById('modal-cam-btn').textContent = '📷 Camera';
                verifyAndCheckIn(decodedText);
            },
            function() { /* ignore individual scan errors */ }
        ).catch(console.error);
    }

    function stopModalCamera() {
        if (modalScanner) {
            modalScanner.stop().catch(() => {});
            modalScanner = null;
        }
    }
</script>
@endpush
