<?php

use App\Http\Controllers\Api\Search\SearchController;
use Illuminate\Support\Facades\Route;

Route::controller(SearchController::class)
    ->name('v1.users.search')
    ->prefix('/v1/users/search')
    ->group(function (){

        Route::get('/','index')->name('.index');

    }
);