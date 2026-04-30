<?php
// routes/api/v1/users/checkout.php

use App\Http\Controllers\Api\Payment\CheckoutController;
use Illuminate\Support\Facades\Route;

// Guest checkout status (no auth required, no store context)
Route::prefix('/v1/users/checkout')
    ->controller(CheckoutController::class)
    ->withoutMiddleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/status/{sessionId}', 'status')->name('users.checkout.status');
    });