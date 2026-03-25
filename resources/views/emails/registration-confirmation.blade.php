<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Confirmed – Renewal Summit 2026</title>
    <style>
        body { margin:0; padding:0; background:#f4f4f4; font-family:'Helvetica Neue',Arial,sans-serif; color:#222; }
        .wrapper { max-width:600px; margin:32px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.10); }
        .header { background:#0f1f3d; padding:32px 40px 24px; text-align:center; }
        .header h1 { color:#D4A017; font-size:22px; margin:16px 0 4px; letter-spacing:.04em; font-weight:800; }
        .header p { color:rgba(255,255,255,.75); font-size:13px; margin:0; }
        .body { padding:36px 40px; }
        .greeting { font-size:17px; font-weight:700; color:#0f1f3d; margin-bottom:8px; }
        .intro { color:#444; line-height:1.7; font-size:15px; margin-bottom:28px; }
        .detail-table { width:100%; border-collapse:collapse; margin-bottom:28px; font-size:14px; }
        .detail-table td { padding:8px 12px; border-bottom:1px solid #f0f0f0; }
        .detail-table .label { color:#666; font-size:13px; white-space:nowrap; }
        .detail-table .value { font-weight:700; color:#0f1f3d; }
        .ref-badge { display:inline-block; background:#0f1f3d; color:#D4A017; font-size:20px; font-weight:900; letter-spacing:.08em; padding:5px 16px; border-radius:8px; }
        .section-title { font-size:12px; font-weight:800; color:#888; text-transform:uppercase; letter-spacing:.12em; margin:24px 0 10px; }
        .qr-box { background:#f9f7f0; border:2px solid #D4A017; border-radius:12px; padding:24px; text-align:center; margin-bottom:20px; }
        .qr-box img { max-width:200px; width:100%; border:4px solid #D4A017; border-radius:8px; }
        .qr-note { font-size:12px; color:#666; margin-top:10px; line-height:1.6; }
        .qr-rule { background:#fff8e1; border-left:4px solid #D4A017; padding:10px 14px; border-radius:0 6px 6px 0; font-size:13px; color:#444; margin-bottom:8px; }
        .event-box { background:#0f1f3d; color:#fff; border-radius:10px; padding:20px 24px; margin-bottom:24px; font-size:14px; }
        .ev-label { color:#D4A017; font-weight:700; font-size:12px; text-transform:uppercase; letter-spacing:.1em; }
        .ev-value { color:#fff; font-weight:600; margin-bottom:10px; }
        .btn { display:inline-block; background:#D4A017; color:#fff !important; font-weight:700; font-size:14px; padding:12px 28px; border-radius:8px; text-decoration:none; }
        .footer { background:#f4f4f4; padding:24px 40px; text-align:center; font-size:12px; color:#999; }
        .footer a { color:#D4A017; text-decoration:none; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <h1>Registration Confirmed</h1>
        <p>Renewal Summit 2026 &middot; International Conference &middot; August 17&ndash;21, 2026</p>
    </div>

    <div class="body">

        <p class="greeting">Dear {{ $registration->full_name }},</p>
        <p class="intro">
            Thank you for registering for the <strong>Renewal Summit 2026</strong>. It will be our
            utmost pleasure to host you <strong>August 17th&ndash;21st, 2026</strong>. Your QR code
            for venue entry is attached to this email as a PNG file.
        </p>

        <div class="section-title">Your Registration Details</div>
        <table class="detail-table">
            <tr>
                <td class="label">Reference</td>
                <td class="value"><span class="ref-badge">{{ $registration->reference }}</span></td>
            </tr>
            <tr>
                <td class="label">Full Name</td>
                <td class="value">{{ $registration->full_name }}</td>
            </tr>
            <tr>
                <td class="label">Phone</td>
                <td class="value">{{ $registration->phone }}</td>
            </tr>
            @if($registration->email)
            <tr>
                <td class="label">Email</td>
                <td class="value">{{ $registration->email }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Affiliation</td>
                <td class="value">{{ $registration->affiliation === 'fcc' ? 'FCC Member' : 'Guest / Other' }}</td>
            </tr>
            <tr>
                <td class="label">Registration Fee</td>
                <td class="value" style="color:#D4A017; font-size:16px;">
                    {{ $registration->currency === 'USD'
                        ? '$' . number_format($registration->total_amount) . ' USD'
                        : 'UGX ' . number_format($registration->total_amount) }}
                </td>
            </tr>
        </table>

        <div class="event-box">
            <div class="ev-label">Event</div>
            <div class="ev-value">Renewal Summit 2026 — International Conference</div>
            <div class="ev-label">Theme</div>
            <div class="ev-value">Healthy Church</div>
            <div class="ev-label">Dates</div>
            <div class="ev-value">August 17&ndash;21, 2026</div>
            <div class="ev-label">Venue</div>
            <div class="ev-value" style="margin-bottom:0;">Ggaba Community Church, Kampala, Uganda</div>
        </div>

        <div class="section-title">Your Entry QR Code</div>
        <div class="qr-box">
            @if($qrBase64 ?? null)
                <img src="{{ $qrBase64 }}" alt="Your QR Code" style="max-width:220px; width:100%; border:4px solid #D4A017; border-radius:8px; display:block; margin:0 auto;">
                <p style="margin-top:12px; font-size:13px; color:#555;">
                    <strong>Your QR code image is shown above and also attached to this email.</strong><br>
                    Present it at the venue gate on the day of the event.
                </p>
            @elseif($qrUrl ?? null)
                <a href="{{ $qrUrl }}" target="_blank">
                    <img src="{{ $qrUrl }}" alt="Your QR Code" style="max-width:220px; width:100%; border:4px solid #D4A017; border-radius:8px; display:block; margin:0 auto;">
                </a>
                <p style="margin-top:12px; font-size:13px; color:#555;">
                    <strong>Your QR code image is shown above and also attached to this email.</strong><br>
                    Present it at the venue gate on the day of the event.
                </p>
            @else
                <div style="background:#fff8e1; border:2px dashed #D4A017; border-radius:8px; padding:16px 20px; font-size:14px; color:#555; text-align:center;">
                    <strong>Your QR code is attached to this email</strong><br>
                    Open the attached file <em>RS2026-QRCode-{{ $registration->reference }}.png</em>
                    and present it at the venue gate on the day of the event.
                </div>
            @endif
            <p class="qr-note" style="font-size:12px; color:#666; margin-top:10px;">
                Save the QR code image to your phone or print it out before attending.
            </p>
        </div>

        <div class="section-title">QR Code Usage</div>
        <div class="qr-rule">
            <strong>Single Entry:</strong> Once a person checks in, their QR code is marked used
            for that entry session and cannot be used again for the same entry point.
        </div>
        <div class="qr-rule">
            <strong>Multiple Entries:</strong> You may re-enter the venue within the same day
            (e.g. if you step out temporarily). Each day of the conference requires a fresh scan.
        </div>



        <p style="font-size:13px; color:#666; line-height:1.7;">
            If you have any questions, please contact us at
            <a href="mailto:info.renewalsummit@gmail.com" style="color:#D4A017; font-weight:700;">info.renewalsummit@gmail.com</a>
        </p>

        <p style="font-size:14px; color:#0f1f3d; font-weight:700; margin-top:24px;">
            God bless you!<br>
            <span style="color:#D4A017;">Renewal Summit 2026 Team</span><br>
            <span style="font-size:12px; color:#888; font-weight:400;">Ggaba Community Church, Kampala, Uganda</span>
        </p>
    </div>

    <div class="footer">
        <p>
            &copy; {{ date('Y') }} Renewal Summit 2026 &mdash; Ggaba Community Church<br>
            <a href="mailto:info.renewalsummit@gmail.com">info.renewalsummit@gmail.com</a>
        </p>
        <p style="margin-top:8px; font-size:11px;">
            This email was sent to {{ $registration->email }} because you registered for Renewal Summit 2026.
        </p>
    </div>

</div>
</body>
</html>
