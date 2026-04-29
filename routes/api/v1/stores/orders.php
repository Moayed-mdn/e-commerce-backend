<?php

use App\Http\Controllers\Api\Order\OrderController;
use Illuminate\Support\Facades\Route;

// Guest lookup stays without store context
Route::prefix('/v1/users/orders')
    ->controller(OrderController::class)
    ->group(function () {
        Route::post('/guest/lookup', 'guestLookup');
    });

// Authenticated + store-scoped
Route::middleware(['auth:sanctum', 'store.context'])
    ->prefix('/v1/stores/{store}')
    ->group(function () {
        Route::prefix('orders')
            ->controller(OrderController::class)
            ->group(function () {
                Route::get('/filters', 'filters');
                Route::get('/', 'index');
                Route::get('/{orderNumber}', 'show');
                Route::post('/{orderNumber}/cancel', 'cancel');
                Route::post('/{orderNumber}/reorder', 'reorder');
            });
    });
