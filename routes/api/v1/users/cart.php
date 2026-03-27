<?php

use App\Http\Controllers\Api\CartController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')
    ->prefix('/v1/users/cart')
    ->controller(CartController::class)->group(function () {
                Route::get('/', 'show');
                Route::post('/items', 'addItem');
                Route::patch('/items/{itemId}', 'updateItem');
                Route::delete('/items/{itemId}', 'removeItem');
                Route::delete('/clear', 'clear');
});