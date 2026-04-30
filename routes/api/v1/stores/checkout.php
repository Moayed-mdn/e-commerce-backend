<?php

use App\Http\Controllers\Api\Payment\CheckoutController;
use Illuminate\Support\Facades\Route;

// Store-scoped checkout routes (auth required)
Route::middleware(['auth:sanctum', 'store.context'])
    ->prefix('/v1/stores/{store}')
    ->group(function () {
        Route::prefix('checkout')
            ->controller(CheckoutController::class)
            ->group(function () {
                Route::post('/', 'initiate')->name('stores.checkout.initiate');
                Route::post('/confirm', 'confirm')->name('stores.checkout.confirm');
            });
    });
