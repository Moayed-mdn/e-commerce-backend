<?php
// routes/api/v1/users/order.php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

// ── Guest order lookup (no auth required) ──
Route::prefix('/v1/users/orders')->controller(OrderController::class)->group(function () {
    Route::post('/guest/lookup', 'guestLookup');
});

// ── Authenticated order routes ──
Route::middleware('auth:sanctum')
    ->prefix('/v1/users/orders')
    ->controller(OrderController::class)
    ->group(function () {
        Route::get('/filters',                 'filters');
        Route::get('/',                        'index');
        Route::get('/{orderNumber}',           'show');
        Route::post('/{orderNumber}/cancel',   'cancel');
        Route::post('/{orderNumber}/reorder',  'reorder');
    });