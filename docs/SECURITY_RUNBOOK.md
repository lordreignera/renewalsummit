# Security runbook

This application is a Laravel/PHP application. Security depends on the application code, the web-server setup, secrets, dependencies, and monitoring together.

## Required production environment

Set these values in the server's `.env` file. Do not commit `.env` or paste its secrets into chat or tickets.

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-real-domain.example
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SWAPP_SSL_VERIFY=true
```

`APP_DEBUG=false` prevents stack traces and environment details from being shown to visitors. `SESSION_SECURE_COOKIE=true` prevents the login cookie being sent over plain HTTP. `SWAPP_SSL_VERIFY=true` prevents a man-in-the-middle attack against the payment provider connection.

## Web-server rules

- Apache's DocumentRoot must be the project's `public` directory, never the project root.
- Serve the site only over HTTPS and redirect HTTP to HTTPS.
- Keep `.env`, `vendor`, `storage`, backups, logs, and database dumps outside the public web root.
- Use a database user with only the permissions this application needs; do not use a root database account.
- Keep PHP, Apache, the operating system, and the payment provider credentials patched.

## Safe deployment sequence

Run from the project directory after taking a database backup and reviewing pending migrations:

```powershell
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts
php artisan migrate --force
php artisan optimize
npm ci
npm run build
php artisan test
composer audit --no-dev
npm audit --omit=dev --audit-level=high
```

The explicit `--no-scripts` keeps self-managed installation separate from the reviewed migration step. Composer dependency installation no longer runs migrations automatically. Never run `migrate:fresh` on production.

## Laravel Cloud release

For the Cloud environment connected to this repository, select PHP 8.4. Set the build command to install Composer dependencies and build Vite assets. Set the deploy command to `php artisan migrate --force`, after reviewing pending migrations and taking a database backup. Do not run migrations in both stages.

In the Cloud environment, use the real HTTPS domain for `APP_URL` and set `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, and `SWAPP_SSL_VERIFY=true`. Keep the existing Cloud `APP_KEY`, database attachment, SwApp API credentials, and working callback URL. The current registration callback does not require a separate signing secret: it checks each transaction through SwApp's authenticated `/getstatus` API before recording a result.

Verify that the existing SwApp, mail, SMS, and QR storage credentials are present in Cloud. This app uses the `r2` disk for QR images by default; it needs `R2_ACCESS_KEY_ID`, `R2_SECRET_ACCESS_KEY`, `R2_BUCKET`, and `R2_ENDPOINT` when `QR_DISK=r2`. Use Cloud Secrets for credentials. Do not copy the local `.env` file to Cloud.

For live SMS, use production Africa's Talking credentials and `AFRICAISTALKING_BASE_URL=https://api.africastalking.com`. Dashboard QR resend now includes the protected QR link in the SMS as well as the email, so attendees without an email address can receive a replacement pass. Test one resend before relying on it for the event.

## Payment callback rule

The registration callback is a notification only. The application uses the existing SwApp API credentials to query `/getstatus` for the stored request ID, ignores duplicate successful callbacks, and does not allow a successful payment to be changed back to a failed state. An unconfirmed status leaves the payment pending so the existing polling flow can retry. No new SwApp secret is required for this flow.

## Incident response

If the site is defaced, redirects visitors, sends spam, or shows unexpected payment activity:

1. Put the site in maintenance mode or restrict it at the web server.
2. Preserve logs and the database; do not immediately delete evidence.
3. Rotate application, database, mail, payment, storage, and server credentials.
4. Check admin logins, new users, scheduled tasks, uploaded files, and recent deployments.
5. Restore only from a known-clean backup, patch the entry point, then bring the site back gradually.
6. Review payment records with the provider before treating a transaction as paid.
