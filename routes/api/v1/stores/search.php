<?php

use App\Http\Controllers\Api\Search\SearchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['store.context'])
    ->prefix('/v1/stores/{store}')
    ->group(function () {
        Route::prefix('search')
            ->controller(SearchController::class)
            ->name('stores.search')
            ->group(function () {
                Route::get('/', 'index')->name('.index');
            });
    });
