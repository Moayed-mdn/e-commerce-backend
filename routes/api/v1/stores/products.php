<?php

use App\Http\Controllers\Api\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['store.context'])
    ->prefix('/v1/stores/{store}')
    ->group(function () {
        Route::prefix('products')
            ->controller(ProductController::class)
            ->name('v1.stores.product')
            ->group(function () {
                Route::get('/', 'index')->name('.index');
                Route::get(
                    '/category/{slug}', 
                    'indexByCategory'
                )->name('.category');
                Route::get(
                    '/{slug}/related', 
                    'related'
                )->name('.related');
                Route::get('/{slug}', 'show')->name('.show');
            });
    });
