Registration Confirmed - Renewal Summit 2026
============================================

Dear {{ $registration->full_name }},

Thank you for registering for the Renewal Summit 2026. It will be our utmost
pleasure to host you August 17th-21st, 2026.

Your QR code for venue entry is attached to this email as:
  RS2026-QRCode-{{ $registration->reference }}.png

Please save it to your phone or print it out before attending.

------------------------------------------------------------
YOUR REGISTRATION DETAILS
------------------------------------------------------------
Reference     : {{ $registration->reference }}
Full Name     : {{ $registration->full_name }}
Phone         : {{ $registration->phone }}
@if($registration->email)
Email         : {{ $registration->email }}
@endif
Affiliation   : {{ $registration->affiliation === 'fcc' ? 'FCC Member' : 'Guest / Other' }}
Fee Paid      : {{ $registration->currency === 'USD' ? '$' . number_format($registration->total_amount) . ' USD' : 'UGX ' . number_format($registration->total_amount) }}

------------------------------------------------------------
EVENT DETAILS
------------------------------------------------------------
Event  : Renewal Summit 2026 - International Conference
Theme  : Healthy Church
Dates  : August 17-21, 2026
Venue  : Ggaba Community Church, Kampala, Uganda

------------------------------------------------------------
QR CODE USAGE
------------------------------------------------------------
- Single Entry: Present your attached QR code at the venue gate for check-in.
  Once scanned, it is marked used for that entry session.
- Multiple Entries: You may re-enter the venue within the same day.
  Each day of the conference requires a fresh scan.

------------------------------------------------------------

If you have any questions, please contact us at:
info.renewalsummit@gmail.com

God bless you!
Renewal Summit 2026 Team
Ggaba Community Church, Kampala, Uganda

------------------------------------------------------------
This email was sent to {{ $registration->email }} because you registered
for Renewal Summit 2026.
(c) {{ date('Y') }} Renewal Summit 2026
