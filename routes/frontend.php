<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\CustomerAuthController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\TaxController as ControllersTaxController;

/* =====================================================
   PUBLIC ROUTES (No Authentication Required)
===================================================== */

// Home Page
Route::get('/', function () {
    return view('frontend.home');
})->name('frontend.home');

/* =====================================================
   AUTHENTICATION ROUTES
===================================================== */

// Login Routes
Route::get('/customer/login', [CustomerAuthController::class, 'showLogin'])
    ->name('customer.login');

Route::post('/customer/login', [CustomerAuthController::class, 'login'])
    ->name('customer.login.post');

// Register Routes
Route::get('/customer/register', [CustomerAuthController::class, 'showRegister'])
    ->name('customer.register');

Route::post('/customer/register', [CustomerAuthController::class, 'register'])
    ->name('customer.register.post');

// Logout Route
Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])
    ->name('customer.logout')
    ->middleware('customer.auth');

/* =====================================================
   PROTECTED ROUTES (Login Required)
===================================================== */

Route::post('/cart/add', [CartController::class, 'add'])
    ->name('cart.add');

Route::get('/frontend/products', [ProductController::class, 'index'])
    ->name('frontend.products.index');

Route::get('/frontend/products/{product}', [ProductController::class, 'show'])
    ->name('frontend.products.show');

// Cart routes - some require authentication
Route::middleware('customer.auth')->group(function () {

    // Customer profile
    Route::get('/customer/profile', [CustomerAuthController::class, 'profile'])
        ->name('customer.profile');

    Route::get('/cart', [CartController::class, 'index'])
        ->name('frontend.cart');

    Route::post('/cart/update', [CartController::class, 'update'])
        ->name('cart.update');

    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])
        ->name('cart.remove');

    Route::delete('/cart/clear', [CartController::class, 'clear'])
        ->name('cart.clear');

    // Discount routes
    Route::get('/cart/discounts', [CartController::class, 'getValidDiscounts'])
        ->name('cart.discounts');

    Route::post('/cart/apply-discount', [CartController::class, 'applyDiscount'])
        ->name('cart.apply-discount');

    Route::post('/cart/remove-discount', [CartController::class, 'removeDiscount'])
        ->name('cart.remove-discount');

    Route::get('/customer/profile', [CustomerAuthController::class, 'profile'])
        ->name('customer.profile');

    // Logout
    Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])
        ->name('customer.logout');

    //customer checkout address
    Route::post('/checkout/address/store', [CheckoutController::class, 'storeAddress'])
        ->name('checkout.address.store');

    Route::get('/checkout/addresses', [CheckoutController::class, 'getAddresses'])
        ->name('checkout.addresses');

    Route::get('/checkout/addresses', [CheckoutController::class, 'addresses'])
        ->name('checkout.addresses');

    Route::post('/checkout/address/store', [CheckoutController::class, 'storeAddress'])
        ->name('checkout.address.store');

    Route::get('/checkout/review/{address}', [CheckoutController::class, 'review'])
        ->name('checkout.review');

    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])
        ->name('checkout.process');


    Route::get('/orders', [OrderController::class, 'index'])->name('frontend.orders');
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('frontend.order.show');
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('frontend.order.cancel');
});
