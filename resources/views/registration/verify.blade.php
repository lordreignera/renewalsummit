@extends('layouts.public')
@section('title', 'QR Verification | Renewal Summit 2026')

@section('content')
<div style="max-width:520px;margin:0 auto;padding:40px 16px 60px;">

@if($reg)
@php
    $originMap = [
        'local'         => ['label' => 'Uganda',         'bg' => '#dcfce7', 'color' => '#166534', 'icon' => '🇺🇬'],
        'africa'        => ['label' => 'Rest of Africa',  'bg' => '#fef9c3', 'color' => '#854d0e', 'icon' => '🌍'],
        'international' => ['label' => 'International',  'bg' => '#dbeafe', 'color' => '#1d4ed8', 'icon' => '✈️'],
    ];
    $origin  = $originMap[$reg->country_type] ?? ['label' => ucfirst($reg->country_type ?? ''), 'bg' => '#f3f4f6', 'color' => '#374151', 'icon' => '🌐'];
    $checked = $reg->isCheckedIn();
    $desig   = $reg->designation . ($reg->designation_specify ? ' – ' . $reg->designation_specify : '');
@endphp

{{-- Status Banner --}}
<div style="background:{{ $checked ? '#16a34a' : '#1a2a4a' }};border-radius:18px 18px 0 0;padding:28px 28px 20px;text-align:center;">
    <div style="font-size:56px;margin-bottom:8px;">{{ $checked ? '✅' : '🎫' }}</div>
    <div style="color:#f5c518;font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;margin-bottom:6px;">
        {{ $checked ? 'Already Checked In' : 'Valid Registration' }}
    </div>
    <div style="color:#fff;font-size:26px;font-weight:800;line-height:1.2;">{{ $reg->full_name }}</div>
    <div style="margin-top:10px;">
        <span style="background:{{ $origin['bg'] }};color:{{ $origin['color'] }};padding:5px 16px;border-radius:20px;font-size:13px;font-weight:700;">
            {{ $origin['icon'] }} {{ $origin['label'] }}
        </span>
    </div>
</div>

{{-- Details Card --}}
<div style="background:#fff;border-radius:0 0 18px 18px;box-shadow:0 8px 32px rgba(0,0,0,.12);overflow:hidden;">

    {{-- Core Details --}}
    <div style="padding:24px 28px;border-bottom:2px solid #f1f5f9;">
        <div style="font-size:11px;font-weight:700;color:#f5c518;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:14px;">Attendee Details</div>
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            <tr style="border-bottom:1px solid #f3f3f3;">
                <td style="padding:9px 0;color:#94a3b8;font-weight:600;width:140px;">Reference</td>
                <td style="padding:9px 0;font-family:monospace;font-weight:800;color:#1a2a4a;">{{ $reg->reference }}</td>
            </tr>
            <tr style="border-bottom:1px solid #f3f3f3;">
                <td style="padding:9px 0;color:#94a3b8;font-weight:600;">Designation</td>
                <td style="padding:9px 0;font-weight:600;">{{ $desig ?: '—' }}</td>
            </tr>
            <tr style="border-bottom:1px solid #f3f3f3;">
                <td style="padding:9px 0;color:#94a3b8;font-weight:600;">Nationality</td>
                <td style="padding:9px 0;">{{ $reg->nationality ?: '—' }}</td>
            </tr>
            <tr style="border-bottom:1px solid #f3f3f3;">
                <td style="padding:9px 0;color:#94a3b8;font-weight:600;">Phone</td>
                <td style="padding:9px 0;">{{ $reg->phone ?: '—' }}</td>
            </tr>
            <tr style="border-bottom:1px solid #f3f3f3;">
                <td style="padding:9px 0;color:#94a3b8;font-weight:600;">Email</td>
                <td style="padding:9px 0;word-break:break-all;font-size:13px;">{{ $reg->email ?: '—' }}</td>
            </tr>
            <tr style="border-bottom:1px solid #f3f3f3;">
                <td style="padding:9px 0;color:#94a3b8;font-weight:600;">Amount Paid</td>
                <td style="padding:9px 0;font-weight:800;color:#16a34a;">{{ $reg->formattedTotal }}</td>
            </tr>
            <tr>
                <td style="padding:9px 0;color:#94a3b8;font-weight:600;">Status</td>
                <td style="padding:9px 0;">
                    <span style="padding:3px 12px;border-radius:20px;font-size:12px;font-weight:700;
                        background:{{ $checked ? '#e0f2fe' : '#dcfce7' }};
                        color:{{ $checked ? '#0369a1' : '#166534' }};">
                        {{ $checked ? '✓ Checked In' : 'Paid – Awaiting Check-In' }}
                    </span>
                </td>
            </tr>
            @if($checked && $reg->checked_in_at)
            <tr>
                <td style="padding:9px 0;color:#94a3b8;font-weight:600;">Check-In Time</td>
                <td style="padding:9px 0;font-weight:600;">{{ $reg->checked_in_at->format('D d M Y · H:i') }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- FCC Member Details (only if affiliation = fcc) --}}
    @if($reg->affiliation === 'fcc')
    <div style="padding:24px 28px;background:#fefce8;border-bottom:2px solid #fde68a;">
        <div style="font-size:11px;font-weight:700;color:#92400e;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:14px;">⛪ FCC Member Details</div>
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
            @if($reg->fcc_region)
            <tr style="border-bottom:1px solid #fde68a;">
                <td style="padding:9px 0;color:#92400e;font-weight:600;width:140px;">Region</td>
                <td style="padding:9px 0;font-weight:600;">{{ $reg->fcc_region }}</td>
            </tr>
            @endif
            @if($reg->fcc_regional_leader)
            <tr style="border-bottom:1px solid #fde68a;">
                <td style="padding:9px 0;color:#92400e;font-weight:600;">Regional Leader</td>
                <td style="padding:9px 0;">{{ $reg->fcc_regional_leader }}</td>
            </tr>
            @endif
            @if($reg->fcc_church)
            <tr style="border-bottom:1px solid #fde68a;">
                <td style="padding:9px 0;color:#92400e;font-weight:600;">Church</td>
                <td style="padding:9px 0;font-weight:600;">{{ $reg->fcc_church }}</td>
            </tr>
            @endif
            @if($reg->fcc_pastor)
            <tr>
                <td style="padding:9px 0;color:#92400e;font-weight:600;">Pastor</td>
                <td style="padding:9px 0;">{{ $reg->fcc_pastor }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    {{-- Footer --}}
    <div style="padding:20px 28px;text-align:center;background:#f8fafc;">
        <div style="font-size:12px;color:#94a3b8;">Renewal Summit 2026 · Ggaba Community Church, Uganda</div>
        <div style="font-size:11px;color:#cbd5e1;margin-top:4px;">August 17th–21st, 2026</div>
    </div>
</div>

@else
{{-- Invalid QR --}}
<div style="background:#fff;border-radius:18px;box-shadow:0 8px 32px rgba(0,0,0,.12);padding:48px 32px;text-align:center;">
    <div style="font-size:64px;margin-bottom:16px;">❌</div>
    <h1 style="color:#dc2626;font-size:22px;font-weight:800;margin-bottom:8px;">Invalid QR Code</h1>
    <p style="color:#6b7280;margin-bottom:24px;">This QR code is not valid or the registration has not been confirmed.</p>
    <a href="{{ route('home') }}"
       style="display:inline-block;background:#1a2a4a;color:#fff;font-weight:700;padding:12px 32px;border-radius:12px;text-decoration:none;">
        ← Back to Home
    </a>
</div>
@endif

</div>
@endsection
