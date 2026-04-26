<?php

use App\Http\Controllers\Api\Category\CategoryController;
use Illuminate\Support\Facades\Route;

Route::controller(CategoryController::class)
    ->name('v1.users.category')
    ->prefix('/v1/users/categories')
    ->group(function (){

        Route::get('/{category:slug}/breadcrumb','breadcrumb')->name('.breadcrumb');


    });
