<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\users\UserController;
use Illuminate\Support\Facades\Route;


// Route::middleware('web')->group(function () {
//     require __DIR__ . '/frontend.php';
// });
//////////////////////////


Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {

    Route::resource('products', ProductController::class);
    Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
        ->name('products.toggle-status');

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
});

require __DIR__ . '/auth.php';
