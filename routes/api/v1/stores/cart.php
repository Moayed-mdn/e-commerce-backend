<?php

use App\Http\Controllers\Api\Cart\CartController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'store.context'])
    ->prefix('/v1/stores/{store}')
    ->group(function () {
        Route::prefix('cart')
            ->controller(CartController::class)
            ->group(function () {
                Route::get('/', 'show');
                Route::post('/items', 'addItem');
                Route::patch('/items/{itemId}', 'updateItem');
                Route::delete('/items/{itemId}', 'removeItem');
                Route::delete('/clear', 'clear');
            });
    });
