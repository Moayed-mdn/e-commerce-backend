<?php
// routes/api/v1/users/product.php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::controller(ProductController::class)
    ->name('v1.users.product')
    ->prefix('/v1/users/products')
    ->group(function () {
        Route::get('/', 'index')->name('.index');

        // ── Changed: string parameter instead of model binding ──
        Route::get('/category/{slug}', 'indexByCategory')->name('.category');

        Route::get('/{slug}/related', 'related')->name('.related');
        Route::get('/{slug}', 'show')->name('.show');
    });