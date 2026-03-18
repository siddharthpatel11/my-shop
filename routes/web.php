<?php

use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\ImportExport\ProductImportExportController;
use App\Http\Controllers\ImportExport\OrderImportExportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\LayoutSettingController;  // ← ADD THIS
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\MetaTagController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\NotificationController;

use App\Http\Controllers\Auth\TwoFactorController;

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'prevent-back', '2fa'])->name('dashboard');

Route::middleware(['auth', '2fa'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 2FA Routes
    Route::prefix('admin/2fa')->name('admin.2fa.')->group(function () {
        Route::get('/verify', [TwoFactorController::class, 'showVerifyForm'])->name('verify');
        Route::post('/verify', [TwoFactorController::class, 'verify']);
        Route::get('/setup', [TwoFactorController::class, 'showSetupForm'])->name('setup');
        Route::post('/setup', [TwoFactorController::class, 'enable']);
    });
});

Route::middleware(['auth', 'prevent-back', '2fa'])->group(function () {

    Route::get('products/export',          [ProductImportExportController::class, 'export'])->name('products.export');
    Route::post('products/import',         [ProductImportExportController::class, 'import'])->name('products.import');
    Route::get('products/import/template', [ProductImportExportController::class, 'template'])->name('products.import.template');

    Route::resource('products', ProductController::class);
    Route::post('products/check-name', [ProductController::class, 'checkName'])->name('products.check-name');
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');

    // Category Routes
    Route::resource('categories', CategoryController::class);
    Route::patch('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])
        ->name('categories.toggle-status');

    // Size Routes
    Route::resource('sizes', SizeController::class);
    Route::patch('/sizes/{size}/toggle-status', [SizeController::class, 'toggleStatus'])
        ->name('sizes.toggle-status');

    // Color Routes
    Route::resource('colors', ColorController::class);
    Route::patch('/colors/{color}/toggle-status', [ColorController::class, 'toggleStatus'])
        ->name('colors.toggle-status');

    Route::resource('taxes', TaxController::class);
    Route::post('taxes/{tax}/toggle-status', [TaxController::class, 'toggleStatus'])
        ->name('taxes.toggle-status');

    //Discount
    Route::resource('discounts', DiscountController::class);
    Route::patch('discounts/{discount}/toggle-status', [DiscountController::class, 'toggleStatus'])
        ->name('discounts.toggle-status');

    // ============================================
    // LAYOUT SETTINGS ROUTES - ADD THESE
    // ============================================
    Route::get('/layout-settings', [LayoutSettingController::class, 'index'])
        ->name('layout-settings.index');

    Route::put('/layout-settings', [LayoutSettingController::class, 'update'])
        ->name('layout-settings.update');

    Route::delete('/layout-settings/delete-logo/{type}', [LayoutSettingController::class, 'deleteLogo'])
        ->name('layout-settings.delete-logo');
    // ============================================

    //Dynamic Pages Management
    Route::resource('pages', PageController::class);
    Route::patch('/pages/{page}/toggle-status', [PageController::class, 'toggleStatus'])
        ->name('pages.toggle-status');
    Route::post('/pages/{page}/delete-gallery-image', [PageController::class, 'deleteGalleryImage'])
        ->name('pages.delete-gallery-image');
});

Route::middleware(['auth', 'prevent-back', '2fa'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/orders/export', [OrderImportExportController::class, 'export'])
        ->name('orders.export');

    Route::get('/orders', [AdminOrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])
        ->name('orders.show');

    Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])
        ->name('orders.update-status');

    Route::patch('/orders/{id}/payment', [AdminOrderController::class, 'updatePaymentStatus'])
        ->name('orders.update-payment-status');

    Route::post('/orders/{id}/notes', [AdminOrderController::class, 'addNotes'])
        ->name('orders.add-notes');

    Route::delete('/orders/{id}', [AdminOrderController::class, 'destroy'])
        ->name('orders.destroy');

    // Route::get('/orders/export/csv', [AdminOrderController::class, 'export'])
    //     ->name('orders.export');

    Route::patch('/orders/{id}/partial-delivery', [AdminOrderController::class, 'processPartialDelivery'])
        ->name('orders.process-partial-delivery');

    Route::post('/fcm-token', [NotificationController::class, 'storeToken'])->name('fcm-token.store');

    // Contact Messages
    Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{id}', [AdminContactController::class, 'show'])->name('contacts.show');
    Route::delete('/contacts/{id}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');

    // Global Meta Tags Management
    Route::get('/meta-tags', [MetaTagController::class, 'index'])->name('meta-tags.index');
    Route::get('/meta-tags/{identifier}/edit', [MetaTagController::class, 'edit'])->name('meta-tags.edit');
    Route::put('/meta-tags/{identifier}', [MetaTagController::class, 'update'])->name('meta-tags.update');
});

require __DIR__ . '/auth.php';
