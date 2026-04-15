<?php

use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ColorApiController;
use App\Http\Controllers\Api\Customer\AddressController;
use App\Http\Controllers\Api\Customer\CartController;
use App\Http\Controllers\Api\Customer\ClearOldCartItemsController;
use App\Http\Controllers\Api\Customer\CustomerAuthController;
use App\Http\Controllers\Api\Customer\OrderController;
use App\Http\Controllers\Api\DatabaseBackupApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\SizeApiController;
use App\Http\Controllers\Api\TranslationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ============================================================
// API v1
// ============================================================

Route::prefix('v1')->group(function () {

    // ── Translations ─────────────────────────────────────────
    Route::get('translations', [TranslationController::class, 'index']);

    // ── Color ────────────────────────────────────────────────
    Route::get('colors', [ColorApiController::class, 'index']);
    Route::post('colors', [ColorApiController::class, 'store']);
    Route::get('colors/{color}', [ColorApiController::class, 'show']);
    Route::put('colors/{color}', [ColorApiController::class, 'update']);
    Route::delete('colors', [ColorApiController::class, 'destroy']);
    Route::post('colors/{color}/toggle-status', [ColorApiController::class, 'toggleStatus']);

    // ── Size ─────────────────────────────────────────────────
    Route::get('sizes', [SizeApiController::class, 'index']);
    Route::post('sizes', [SizeApiController::class, 'store']);
    Route::get('sizes/{size}', [SizeApiController::class, 'show']);
    Route::put('sizes/{size}', [SizeApiController::class, 'update']);
    Route::delete('sizes', [SizeApiController::class, 'destroy']);
    Route::post('sizes/{size}/toggle-status', [SizeApiController::class, 'toggleStatus']);

    // ── Category ─────────────────────────────────────────────
    Route::get('categories', [CategoryApiController::class, 'index']);
    Route::post('categories', [CategoryApiController::class, 'store']);
    Route::get('categories/{category}', [CategoryApiController::class, 'show']);
    Route::put('categories/{category}', [CategoryApiController::class, 'update']);
    Route::delete('categories', [CategoryApiController::class, 'destroy']);
    Route::post('categories/{category}/toggle-status', [CategoryApiController::class, 'toggleStatus']);

    // ── Product ──────────────────────────────────────────────
    Route::get('products/form-data', [ProductApiController::class, 'formData']);
    Route::post('products/{product}/toggle-status', [ProductApiController::class, 'toggleStatus']);
    Route::get('products', [ProductApiController::class, 'index']);
    Route::post('products', [ProductApiController::class, 'store']);
    Route::get('products/{product}', [ProductApiController::class, 'show']);
    Route::put('products/{product}', [ProductApiController::class, 'update']);
    Route::delete('products', [ProductApiController::class, 'destroy']);

    // ── Database Backup ───────────────────────────────────────
    // Route::get('database/backup', [DatabaseBackupApiController::class, 'backup'])->name('api.database.backup');
    // Route::get('database/backups', [DatabaseBackupApiController::class, 'list'])->name('api.database.list');
    // Route::get('database/download/{filename}', [DatabaseBackupApiController::class, 'download'])->name('api.database.download');
    // Route::delete('database/backup/{filename}', [DatabaseBackupApiController::class, 'destroy'])->name('api.database.destroy');

    // ============================================================
    // Customer API Group
    // ============================================================
    Route::prefix('customer')->name('api.customer.')->group(function () {

        // ── Auth (Public) ─────────────────────────────────────
        Route::post('register', [CustomerAuthController::class, 'register'])->name('register');
        Route::post('login', [CustomerAuthController::class, 'login'])->name('login');
        Route::post('refresh-token', [CustomerAuthController::class, 'refreshToken'])->name('refresh-token');
        Route::post('verify-2fa', [CustomerAuthController::class, 'verify2FA'])->name('verify-2fa');
        // Forgot Password API
        Route::post('forgot-password', [CustomerAuthController::class, 'forgotPassword']);
        Route::post('reset-password', [CustomerAuthController::class, 'resetPassword']);

        // ── Products (Public Customer Facing) ─────────────────

        // ── Protected Customer Routes ─────────────────────────
        // Route::middleware('auth:customer-api', 'validate.session')->group(function () {
        Route::middleware('auth:sanctum', 'validate.session')->group(function () {

            Route::get('products', [\App\Http\Controllers\Api\Customer\ProductController::class, 'index'])->name('products.index');
            Route::get('products/{id}', [\App\Http\Controllers\Api\Customer\ProductController::class, 'show'])->name('products.show');

            // Auth
            Route::post('logout', [CustomerAuthController::class, 'logout'])->name('logout');
            // Route::get('profile', [CustomerAuthController::class, 'profile'])->name('profile');
            Route::match(['GET', 'POST'],'profile', [CustomerAuthController::class, 'profile'])->name('profile');
            Route::post('profile/update', [CustomerAuthController::class, 'updateProfile'])->name('profile.update');
            Route::post('profile/remove-avatar', [CustomerAuthController::class, 'removeAvatar'])->name('profile.remove-avatar');

            // ── Google 2FA Management ────────────────────────
            Route::prefix('2fa')->group(function () {
                Route::get('setup', [CustomerAuthController::class, 'setup2FA']);
                Route::post('enable', [CustomerAuthController::class, 'enable2FA']);
                Route::post('disable', [CustomerAuthController::class, 'disable2FA']);
            });

            // ── Email Change ──────────────────────────────────
            Route::prefix('email-change')->group(function () {
                Route::post('send-otp', [CustomerAuthController::class, 'sendEmailChangeOTP']);
                Route::post('verify-otp', [CustomerAuthController::class, 'verifyEmailChangeOTP']);
                Route::post('update', [CustomerAuthController::class, 'updateEmail']);
            });

            // ── Phone Change ──────────────────────────────────
            Route::prefix('phone-change')->group(function () {
                Route::post('send-otp', [CustomerAuthController::class, 'sendPhoneChangeOTP']);
                Route::post('verify-otp', [CustomerAuthController::class, 'verifyPhoneChangeOTP']);
                Route::post('update', [CustomerAuthController::class, 'updatePhone']);
            });

            // ── Cart ──────────────────────────────────────────
            Route::prefix('cart')->name('cart.')->group(function () {
                Route::get('/', [CartController::class, 'index'])->name('index');
                Route::post('/', [CartController::class, 'store'])->name('store');
                Route::put('/{id}', [CartController::class, 'update'])->name('update');
                Route::delete('/{id}', [CartController::class, 'destroy'])->name('destroy');
                Route::delete('/', [CartController::class, 'clear'])->name('clear');
                Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('apply-discount');
                Route::post('/buy-now', [CartController::class, 'buyNow'])->name('buy-now');
            });

            // ── Clear Old Cart Items ──────────────────────────

            Route::get('/cart/clear-old', [ClearOldCartItemsController::class, 'getCart']);

            // ── Addresses ─────────────────────────────────────
            Route::prefix('addresses')->name('addresses.')->group(function () {
                Route::get('/', [AddressController::class, 'index'])->name('index');
                Route::post('/', [AddressController::class, 'store'])->name('store');
                Route::get('/{id}', [AddressController::class, 'show'])->name('show');
                Route::put('/{id}', [AddressController::class, 'update'])->name('update');
                // Route::delete('/{id}', [AddressController::class, 'destroy'])->name('destroy');
                Route::delete('/destroy', [AddressController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/set-default', [AddressController::class, 'setDefault'])->name('set-default');
            });

            // ── Checkout Review ──────────────────────────────
            Route::get('checkout/review/{address_id}', [OrderController::class, 'checkoutReview'])->name('checkout-review');

            // ── Orders ────────────────────────────────────────
            Route::prefix('order')->name('orders.')->group(function () {
                Route::get('/', [OrderController::class, 'index'])->name('index');
                Route::post('/', [OrderController::class, 'store'])->name('store');
                Route::get('/{id}', [OrderController::class, 'show'])->name('show');
                Route::post('/{id}/cancel', [OrderController::class, 'cancel'])->name('cancel');
                Route::post('/verify-payment', [OrderController::class, 'verifyPayment'])->name('verify-payment');
            });
        });
    });
});
