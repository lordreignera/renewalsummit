<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistration;
// use App\Http\Controllers\DonationController; // DISABLED: awaiting PayPal approval
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/* ─────────────────────────────────────────────────────────────
 | Public – Landing Page
 |────────────────────────────────────────────────────────────*/
Route::get('/', fn() => view('home'))->name('home');

/* ─────────────────────────────────────────────────────────────
 | Registration Flow (multi-step)
 |────────────────────────────────────────────────────────────*/
Route::prefix('register')->name('register.')->group(function () {
    Route::get('/',         [RegistrationController::class, 'start'])->name('start');
    Route::post('/resume',  [RegistrationController::class, 'resume'])->name('resume');

    // Step dynamic routing
    Route::get('/step/1',   [RegistrationController::class, 'step1'])->name('step1');
    Route::post('/step/1',  [RegistrationController::class, 'saveStep1'])->name('step1.save');
    Route::get('/step/2',   [RegistrationController::class, 'step2'])->name('step2');
    Route::post('/step/2',  [RegistrationController::class, 'saveStep2'])->name('step2.save');
    Route::get('/step/3',   [RegistrationController::class, 'step3'])->name('step3');
    Route::post('/step/3',  [RegistrationController::class, 'submitPayment'])->name('step3.pay');

    // Helper: resolve /register/step/N to named route
    Route::get('/step/{step}', function (int $step) {
        return redirect()->route("register.step{$step}");
    })->where('step', '[1-3]')->name('step');
});

Route::get('/registration/pending/{reference}', [RegistrationController::class, 'pending'])->name('register.pending');
Route::get('/registration/complete',            [RegistrationController::class, 'complete'])->name('register.complete');

/* ─────────────────────────────────────────────────────────────
 | QR Verification
 |────────────────────────────────────────────────────────────*/
Route::get('/verify/{token}', [RegistrationController::class, 'verify'])->name('register.verify');

/* ─────────────────────────────────────────────────────────────
 | Payment Callbacks (called by Swapp gateway, NOT by user)
 |────────────────────────────────────────────────────────────*/
Route::post('/payment/callback',  [PaymentController::class, 'callback'])->name('payment.callback');
// Route::post('/donation/callback', [PaymentController::class, 'donationCallback'])->name('donation.callback'); // DISABLED: awaiting PayPal approval

// Polling endpoint (AJAX)
Route::get('/payment/status/{reference}', [PaymentController::class, 'status'])->name('payment.status');

/* ─────────────────────────────────────────────────────────────
 | Donations — DISABLED: awaiting PayPal approval
 | Uncomment below once PayPal approves the account
 |────────────────────────────────────────────────────────────*/
// Route::get('/donate',  [DonationController::class, 'show'])->name('donate');
// Route::post('/donate', [DonationController::class, 'store'])->name('donate.store');

/* ─────────────────────────────────────────────────────────────
 | Admin – Protected
 |────────────────────────────────────────────────────────────*/
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

        // Registrations CRUD & actions
        Route::prefix('registrations')->name('registrations.')->group(function () {
            Route::get('/',                      [AdminRegistration::class, 'index'])->name('index');
            Route::get('/export',                [AdminRegistration::class, 'export'])->name('export');
            Route::get('/{registration}',        [AdminRegistration::class, 'show'])->name('show');
            Route::post('/{registration}/resend-qr',    [AdminRegistration::class, 'resendQr'])->name('resend-qr');
        });

        // Check-in
        Route::get('/checkin',          [AdminRegistration::class, 'checkInPage'])->name('checkin');
        Route::get('/checkin/{token}',  [AdminRegistration::class, 'checkIn'])->name('checkin.process');

        // Redirect old /dashboard to admin dashboard
        Route::get('/jetstream-dashboard', fn() => redirect()->route('admin.dashboard'));
    });

// Override Jetstream default /dashboard → send admins to our admin dashboard
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->get('/dashboard', fn() => redirect()->route('admin.dashboard'))
    ->name('dashboard');

