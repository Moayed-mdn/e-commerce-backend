<?php
// routes/api/v1/users/checkout.php

use App\Http\Controllers\Api\Payment\CheckoutController;
use Illuminate\Support\Facades\Route;

// Guest checkout (no auth required)
Route::prefix('/v1/users/checkout')->controller(CheckoutController::class)->group(function () {
    Route::post('/session', 'createSession');
    Route::get('/status/{sessionId}', 'status');
});

// Logged-in checkout (auth optional — controller checks inside)
Route::middleware('auth:sanctum')
    ->prefix('/v1/users/checkout')
    ->controller(CheckoutController::class)
    ->group(function () {
        Route::post('/session/auth', 'createSession');
    });