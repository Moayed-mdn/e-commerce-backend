<?php

use App\Http\Controllers\Api\Homepage\HomePageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['store.context'])
    ->group(function () {
        Route::prefix('homepage')
            ->controller(HomePageController::class)
            ->name('stores.homepage')
            ->group(function () {
                Route::get('/best-seller', 'bestSeller')->name('.best-seller');
                Route::get('/hero', 'hero')->name('.hero');
            });
    });
