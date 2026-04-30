<?php

use App\Http\Controllers\Api\Address\AddressController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'store.context'])
    ->prefix('/v1/stores/{store}')
    ->group(function () {
        Route::prefix('addresses')
            ->controller(AddressController::class)
            ->group(function () {
                Route::get('/', 'index')->name('stores.addresses.index');
                Route::post('/', 'store')->name('stores.addresses.store');
                Route::put('/{address}', 'update')->name('stores.addresses.update');
                Route::delete('/{address}', 'destroy')->name('stores.addresses.destroy');
                Route::patch('/{address}/default', 'setDefault')->name('stores.addresses.setDefault');
            });
    });
