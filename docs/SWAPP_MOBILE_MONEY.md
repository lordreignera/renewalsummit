# SwApp Mobile Money Integration — Renewal Summit 2026

## Overview

This project uses the **SwApp Mobile Money API** to collect registration payments via MTN MoMo and Airtel Money (Uganda). The API follows OAuth 2.0 and REST principles.

---

## Credentials & Configuration

All credentials are stored in `.env` and loaded via `config/services.php`:

```env
SWAPP_BASE_URL=https://www.swapp.co.ug/api/mm    # production
SWAPP_CLIENT_ID=38316
SWAPP_API_KEY=<your_api_key>
SWAPP_API_SECRET=<your_api_secret>
SWAPP_CALLBACK_URL="${APP_URL}/payment/callback"
```

> **Test environment:** Change `SWAPP_BASE_URL` to `https://www.swapp.co.ug/apitest/mm`  
> **Production environment:** Use `https://www.swapp.co.ug/api/mm`  
> The rest of the code changes automatically — only this one line differs.

`config/services.php` entry:
```php
'swapp' => [
    'base_url'     => env('SWAPP_BASE_URL', 'https://www.swapp.co.ug/api/mm'),
    'client_id'    => env('SWAPP_CLIENT_ID'),
    'api_key'      => env('SWAPP_API_KEY'),
    'api_secret'   => env('SWAPP_API_SECRET'),
    'callback_url' => env('SWAPP_CALLBACK_URL'),
],
```

---

## API Endpoints Used

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/token` | Get OAuth Bearer token |
| POST | `/balance` | Check merchant account balance |
| POST | `/collect` | Send USSD prompt to customer (collect payment) |
| POST | `/getstatus` | Poll transaction status by RequestId |
| POST | `/payout` | Send money TO a phone number (disbursement) |

---

## Authentication Flow

### Step 1 — Get Bearer Token

```
POST /token
Header: Swapp-Client-ID: {client_id}
Header: Authorization: Basic base64({api_key}:{api_secret})
Body (form-urlencoded): grant_type=client_credentials
```

**Response:**
```json
{
  "access_token": "eyJ...",
  "expires_in": 36600,
  "client_id": "38316"
}
```

Token is cached for **50 minutes** in Laravel's cache (`swapp_token` key). Use the same token for all requests until it expires.

---

## Collection Flow (Registration Payment)

### Phone Number Format

Per SwApp docs: *"The number should be without international codes or the leading 0."*

| Input | Normalized Account |
|-------|--------------------|
| `0708356505` | `708356505` |
| `256708356505` | `708356505` |
| `+256708356505` | `708356505` |

This is handled in `SwappPaymentService::normalisePhone()`.

---

### Step 2 — Initiate Collection (`/collect`)

```
POST /collect
Header: Swapp-Client-ID: {client_id}
Header: Authorization: Bearer {access_token}
Header: Content-Type: application/json
Body:
{
    "Account":   "708356505",
    "Amount":    50000,
    "RequestId": "6844b37f-40d5-4759-a68e-3694a014e6ba"
}
```

- `Account` — phone digits only, no leading 0, no country code
- `Amount` — integer in UGX, **minimum 3,000 UGX**
- `RequestId` — UUID v4, must be less than 48 characters (use `Str::uuid()`)

**Pending response (USSD sent to phone):**
```json
{
    "status": 100,
    "message": "Transaction submitted, but still awaiting approval",
    "payment": {
        "status": "PENDING",
        "txnid": 1226576,
        "requestId": "6844b37f-40d5-4759-a68e-3694a014e6ba"
    }
}
```

**Failure response:**
```json
{
    "status": 400,
    "message": "LOW_BALANCE_OR_PAYEE_LIMIT_REACHED_OR_NOT_ALLOWED"
}
```

---

### Step 3 — Check Transaction Status (`/getstatus`)

```
POST /getstatus
Header: Swapp-Client-ID: {client_id}
Header: Authorization: Bearer {access_token}
Body: { "RequestId": "6844b37f-40d5-4759-a68e-3694a014e6ba" }
```

Poll this every 3–5 seconds after collection until `status` changes from `PENDING`.

---

### Step 4 — Callback (SwApp → Your Server)

SwApp POSTs to your callback URL when a transaction completes.  
**Callback URL (configure once in SwApp merchant portal):**
```
https://renewalsummit26.laravel.cloud/payment/callback
```

The callback is handled by `PaymentController::callback()` which:
1. Calls `SwappPaymentService::handleCallback()` to update payment status
2. If payment succeeded → generates QR code + sends confirmation email

---

## Complete Registration Payment Flow

```
User fills Step 3 form (phone number + payment method)
    ↓
POST /registration/step/3
    ↓
RegistrationController::submitPayment()
    ↓
SwappPaymentService::initiateMobileMoney($reg, $phone)
    ├── getToken()               → POST /token
    └── POST /collect            → sends USSD to customer's phone
    ↓
Redirect → /registration/pending/{reference}
    ↓
pending.blade.php polls /payment/status/{reference} every 3s
    ↓
Customer sees USSD pop-up → enters MoMo PIN → approves
    ↓
SwApp POSTs to /payment/callback
    ↓
PaymentController::callback()
    ├── handleCallback()         → marks payment + registration as 'paid'
    ├── generateForRegistration()→ creates QR code (stored R2 / local)
    └── sends confirmation email (if MAIL configured)
    ↓
Polling detects status=paid → redirect to /registration/complete
    ↓
User sees confirmation page with QR code
```

---

## Code Files

| File | Role |
|------|------|
| `app/Services/SwappPaymentService.php` | All API calls (token, collect, status, callback handler) |
| `app/Http/Controllers/RegistrationController.php` | `submitPayment()` initiates mobile money, redirects to pending |
| `app/Http/Controllers/PaymentController.php` | Handles SwApp callback, triggers QR + email |
| `app/Console/Commands/TestSwappPayment.php` | CLI test tool: `php artisan swapp:test {phone} {amount}` |
| `config/services.php` | SwApp config (reads from .env) |
| `routes/web.php` | `/payment/callback`, `/payment/status/{ref}`, `/registration/pending/{ref}` |

---

## Testing

```bash
# Test with a phone number (amount must be >= 3000 UGX)
php artisan swapp:test 0708356505 3000

# Check balance only (edit command or use tinker)
php artisan swapp:test 0000000000 3000
```

**Known sandbox behaviour:**
- `apitest` URL responds but does NOT send real USSD prompts to phones
- Use `api` (production) URL for real prompts

**Known issues:**
- `LOW_BALANCE_OR_PAYEE_LIMIT_REACHED_OR_NOT_ALLOWED` — customer has insufficient MoMo balance, or the number has a daily limit, or it may be the merchant's own number
- Token cached — if credentials change, run `php artisan cache:clear`

---

## Merchant Account

| Field | Value |
|-------|-------|
| Client ID | `38316` |
| Contact | `256701155877` |
| Account Status | green |
| Current Balance | varies (check with `swapp:test`) |

---

## Notes

- The callback URL must be configured in the **SwApp merchant portal** (not per-request)
- Minimum collect amount: **3,000 UGX**
- Token expiry: **~10 hours** (`expires_in: 36600`), cached for 50 minutes in Laravel
- `RequestId` must be unique per transaction — UUID v4 via `Str::uuid()`
- VISA/card payments are not yet integrated (currently accepted without actual charge)
