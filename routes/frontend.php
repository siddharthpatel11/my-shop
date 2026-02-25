<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\CustomerAuthController;
use App\Http\Controllers\Frontend\OrderController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\MyPanelController;
use App\Http\Controllers\Frontend\SocialAuthController;
use App\Http\Controllers\TaxController as ControllersTaxController;

/* =====================================================
   PUBLIC ROUTES (No Authentication Required)
===================================================== */

// Home Page
Route::get('/', function () {
    return view('frontend.home');
})->name('frontend.home');

// Contact and Gallery Pages
Route::get('/contact', [PageController::class, 'show'])->defaults('slug', 'contact')->name('contact');
Route::get('/gallery', [PageController::class, 'show'])->defaults('slug', 'gallery')->name('gallery');

// Contact Form Submission (Public)
Route::post('/contact/submit', [ContactController::class, 'submit'])
    ->name('contact.submit');

/* =====================================================
   AUTHENTICATION ROUTES
===================================================== */

// Login Routes
Route::get('/customer/login', [CustomerAuthController::class, 'showLogin'])
    ->name('customer.login');

Route::post('/customer/login', [CustomerAuthController::class, 'login'])
    ->name('customer.login.post');

//Social Login Routes
Route::prefix('customer/auth/{provider}')->group(function () {
    Route::get('redirect', [SocialAuthController::class, 'redirect'])->name('customer.social.redirect');
    Route::get('callback', [SocialAuthController::class, 'callback'])->name('customer.social.callback');
});

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

    // Customer logout
    Route::post('/customer/logout', [CustomerAuthController::class, 'logout'])
        ->name('customer.logout');

    // Customer profile
    Route::get('/customer/profile', [CustomerAuthController::class, 'profile'])
        ->name('customer.profile');

    // Checkout and Orders
    Route::post('/checkout/address/store', [CheckoutController::class, 'storeAddress'])
        ->name('checkout.address.store');
    Route::get('/checkout/addresses', [CheckoutController::class, 'getAddresses'])
        ->name('checkout.addresses');
    Route::get('/checkout/review/{address}', [CheckoutController::class, 'review'])
        ->name('checkout.review');
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])
        ->name('checkout.process');
    Route::post('/checkout/verify-payment', [CheckoutController::class, 'verifyPayment'])
        ->name('checkout.verify-payment');

    Route::get('/orders', [OrderController::class, 'index'])
        ->name('frontend.orders');
    Route::get('/order/{id}', [OrderController::class, 'show'])
        ->name('frontend.order.show');
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancel'])
        ->name('frontend.order.cancel');

    // Wishlist Routes
    Route::get('/wishlist', [WishlistController::class, 'index'])
        ->name('frontend.wishlist');
    Route::post('/wishlist/add', [WishlistController::class, 'store'])
        ->name('wishlist.add');
    Route::delete('/wishlist/remove/{id}', [WishlistController::class, 'destroy'])
        ->name('wishlist.remove');
    Route::delete('/wishlist/clear', [WishlistController::class, 'clear'])
        ->name('wishlist.clear');
    Route::post('/wishlist/remove-multiple', [WishlistController::class, 'removeMultiple'])
        ->name('wishlist.remove-multiple');

    // My Panel Dashboard
    Route::get('/my-panel', [MyPanelController::class, 'index'])
        ->name('frontend.my-panel');
});

// Customer-facing page route (NO auth required)
Route::get('/page/{page}', [PageController::class, 'show'])
    ->name('page.show');
