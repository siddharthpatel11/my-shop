<?php

use App\Http\Controllers\Api\ColorApiController;
use App\Http\Controllers\Api\SizeApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API v1 Routes
Route::prefix('v1')->group(function () {

    // Color API Routes
    Route::get('colors', [ColorApiController::class, 'index']);
    Route::post('colors', [ColorApiController::class, 'store']);
    Route::get('colors/{color}', [ColorApiController::class, 'show']);
    Route::put('colors/{color}', [ColorApiController::class, 'update']);
    Route::delete('colors', [ColorApiController::class, 'destroy']);
    Route::post('colors/{color}/toggle-status', [ColorApiController::class, 'toggleStatus']);
    // Route::apiResource('colors', ColorApiController::class);
    // Route::post('colors/{color}/toggle-status', [ColorApiController::class, 'toggleStatus']);

    // Size API Routes
    Route::get('sizes', [SizeApiController::class, 'index']);
    Route::post('sizes', [SizeApiController::class, 'store']);
    Route::get('sizes/{size}', [SizeApiController::class, 'show']);
    Route::put('sizes/{size}', [SizeApiController::class, 'update']);
    Route::delete('sizes', [SizeApiController::class, 'destroy']);
    Route::post('sizes/{size}/toggle-status', [SizeApiController::class, 'toggleStatus']);
    // Route::apiResource('sizes', SizeApiController::class);
    // Route::post('sizes/{size}/toggle-status', [SizeApiController::class, 'toggleStatus']);

    // Category API Routes
    Route::get('categories', [CategoryApiController::class, 'index']);
    Route::post('categories', [CategoryApiController::class, 'store']);
    Route::get('categories/{category}', [CategoryApiController::class, 'show']);
    Route::put('categories/{category}', [CategoryApiController::class, 'update']);
    Route::delete('categories', [CategoryApiController::class, 'destroy']);
    Route::post('categories/{category}/toggle-status', [CategoryApiController::class, 'toggleStatus']);
    // Route::apiResource('categories', CategoryApiController::class);
    // Route::post('categories/{category}/toggle-status', [CategoryApiController::class, 'toggleStatus']);

    // Product API Routes
    // CRITICAL: form-data MUST be FIRST
    Route::get('products/form-data', [ProductApiController::class, 'formData']);
    Route::post('products/{product}/toggle-status', [ProductApiController::class, 'toggleStatus']);

    // Standard CRUD routes
    Route::get('products', [ProductApiController::class, 'index']);
    Route::post('products', [ProductApiController::class, 'store']);
    Route::get('products/{product}', [ProductApiController::class, 'show']);
    Route::put('products/{product}', [ProductApiController::class, 'update']);
    Route::delete('products', [ProductApiController::class, 'destroy']);
});
