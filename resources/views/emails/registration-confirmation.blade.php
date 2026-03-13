@component('mail::message')
# 🎉 Registration Confirmed!

Dear **{{ $registration->full_name }}**,

Thank you for registering for the **Renewal Summit 2026**. It will be our utmost pleasure to host you
**August 17th–27th 2026**. Below is the QR code for your entry to the venue.

---

## Your Registration Details

| | |
|---|---|
| **Reference**    | `{{ $registration->reference }}` |
| **Name**         | {{ $registration->full_name }} |
| **Phone**        | {{ $registration->phone }} |
@if($registration->email)
| **Email**        | {{ $registration->email }} |
@endif
| **Affiliation**  | {{ $registration->affiliation === 'fcc' ? 'FCC Member' : 'Guest' }} |
| **Fee Paid**     | **{{ $registration->formattedTotal }}** |

---

## 📅 Event Details

- **Event:** Renewal Summit 2026 — International Conference
- **Theme:** Healthy Church
- **Dates:** August 17–27, 2026
- **Venue:** Ggaba Community Church, Uganda

---

## 📲 Your QR Code

Your QR code is attached to this email. **Please present it at the gate** on the day of the event for check-in.

@component('mail::button', ['url' => url('/verify/' . $registration->qr_token), 'color' => 'success'])
View My Registration Online
@endcomponent

If you have any questions, please contact us at renewalsummit@africarenewal.org

**Renewal Summit 2026 Team**
Ggaba Community Church, Uganda
renewal@africarenewal.org
@endcomponent
