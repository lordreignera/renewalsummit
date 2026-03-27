<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\HotelController as AdminHotel;
use App\Http\Controllers\Admin\RegistrationController as AdminRegistration;
// use App\Http\Controllers\Admin\TestimonialVideoController as AdminTestimonialVideo; // DISABLED: testimonial videos paused
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\RoleController as AdminRole;
use App\Http\Controllers\Admin\PermissionController as AdminPermission;
// use App\Http\Controllers\DonationController; // DISABLED: awaiting PayPal approval
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QrImageController;
use App\Http\Controllers\RegistrationController;
// use App\Http\Controllers\TestimonialVideoController; // DISABLED: testimonial videos paused
use Illuminate\Support\Facades\Route;

/* ─────────────────────────────────────────────────────────────
 | Public – Landing Page
 |────────────────────────────────────────────────────────────*/
Route::get('/', function () {
    // DISABLED: testimonial videos paused
    // $approvedVideos = \App\Models\TestimonialVideo::where('status', 'approved')
    //     ->latest('approved_at')
    //     ->take(8)
    //     ->get();

    $hotels = \App\Models\Hotel::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    return view('home', compact('hotels'));
})->name('home');

// DISABLED: testimonial videos paused
// Route::get('/testimonials/videos/{video}/stream', [TestimonialVideoController::class, 'stream'])->name('testimonials.stream');

/* ─────────────────────────────────────────────────────────────
 | QR Code Image (proxy – works for both local & R2 storage)
 |────────────────────────────────────────────────────────────*/
Route::get('/qr/{reference}', [QrImageController::class, 'show'])->name('qr.show');

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
Route::get('/registration/{reference}/{token}/accommodation', [RegistrationController::class, 'accommodation'])->name('register.accommodation');
Route::post('/registration/{reference}/{token}/accommodation', [RegistrationController::class, 'saveAccommodation'])->name('register.accommodation.save');
Route::get('/registration/{reference}/{token}/accommodation/pending', [RegistrationController::class, 'accommodationPending'])->name('register.accommodation.pending');
Route::post('/registration/{reference}/{token}/accommodation/pending/resend', [RegistrationController::class, 'resendAccommodationPrompt'])->name('register.accommodation.pending.resend');

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
Route::get('/payment/accommodation-status/{reference}/{token}', [PaymentController::class, 'accommodationStatus'])->name('payment.accommodation.status');

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

        // ── Dashboard (all roles) ──────────────────────────────────
        Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');

        // ── Registrations (all roles can view; only super_admin can create/manage) ──
        Route::prefix('registrations')->name('registrations.')->group(function () {
            Route::get('/',               [AdminRegistration::class, 'index'])->name('index');
            Route::get('/{registration}', [AdminRegistration::class, 'show'])->name('show');

            // finance + super_admin only
            Route::get('/export', [AdminRegistration::class, 'export'])
                ->middleware('role:super_admin,finance')
                ->name('export');

            // super_admin + registrar: create registrations & send payment prompts
            Route::middleware('role:super_admin,registrar')->group(function () {
                Route::get('/create',                 [AdminRegistration::class, 'create'])->name('create');
                Route::post('/create',                [AdminRegistration::class, 'store'])->name('store');
                Route::get('/{registration}/payment', [AdminRegistration::class, 'payment'])->name('payment');
            });

            // super_admin only
            Route::middleware('role:super_admin')->group(function () {
                Route::post('/{registration}/resend-qr', [AdminRegistration::class, 'resendQr'])->name('resend-qr');
            });
        });

        // ── Check-in (all roles) ───────────────────────────────────
        Route::get('/checkin',                         [AdminRegistration::class, 'checkInPage'])->name('checkin');
        Route::get('/checkin/{token}',                 [AdminRegistration::class, 'checkIn'])->name('checkin.process');
        Route::post('/checkin/{registration}/confirm', [AdminRegistration::class, 'checkInById'])->name('checkin.confirm');

        // ── Super Admin only ───────────────────────────────────────
        Route::middleware('role:super_admin')->group(function () {

            Route::prefix('hotels')->name('hotels.')->group(function () {
                Route::get('/',              [AdminHotel::class, 'index'])->name('index');
                Route::get('/create',        [AdminHotel::class, 'create'])->name('create');
                Route::post('/create',       [AdminHotel::class, 'store'])->name('store');
                Route::get('/{hotel}/edit',  [AdminHotel::class, 'edit'])->name('edit');
                Route::put('/{hotel}',       [AdminHotel::class, 'update'])->name('update');
                Route::delete('/{hotel}',    [AdminHotel::class, 'destroy'])->name('destroy');
            });

            // DISABLED: testimonial videos paused
            // Route::prefix('testimonials')->name('testimonials.')->group(function () {
            //     Route::get('/',                    [AdminTestimonialVideo::class, 'index'])->name('index');
            //     Route::get('/create',              [AdminTestimonialVideo::class, 'create'])->name('create');
            //     Route::post('/',                   [AdminTestimonialVideo::class, 'store'])->name('store');
            //     Route::get('/{video}/review',      [AdminTestimonialVideo::class, 'review'])->name('review');
            //     Route::post('/{video}/viewed',     [AdminTestimonialVideo::class, 'markViewed'])->name('viewed');
            //     Route::post('/{video}/approve',    [AdminTestimonialVideo::class, 'approve'])->name('approve');
            //     Route::post('/{video}/reject',     [AdminTestimonialVideo::class, 'reject'])->name('reject');
            //     Route::delete('/{video}',          [AdminTestimonialVideo::class, 'destroy'])->name('destroy');
            // });

            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/',            [AdminUser::class, 'index'])->name('index');
                Route::get('/create',      [AdminUser::class, 'create'])->name('create');
                Route::post('/',           [AdminUser::class, 'store'])->name('store');
                Route::get('/{user}/edit', [AdminUser::class, 'edit'])->name('edit');
                Route::put('/{user}',      [AdminUser::class, 'update'])->name('update');
                Route::delete('/{user}',   [AdminUser::class, 'destroy'])->name('destroy');
            });

            Route::resource('roles',       AdminRole::class)->except(['show']);
            Route::resource('permissions', AdminPermission::class)->except(['show']);
        });

        Route::get('/jetstream-dashboard', fn() => redirect()->route('admin.dashboard'));
    });

// Override Jetstream default /dashboard → send admins to our admin dashboard
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->get('/dashboard', fn() => redirect()->route('admin.dashboard'))
    ->name('dashboard');

