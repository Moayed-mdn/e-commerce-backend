<?php

use App\Http\Controllers\Api\HomePageController;
use Illuminate\Support\Facades\Route;

Route::controller(HomePageController::class)
    ->name('v1.users.homepage')
    ->prefix('v1/users/homepage')
    ->group(function(){
        Route::get('/best-seller','bestSeller')->name('.bestSeller');
        Route::get('/hero','hero')->name('.hero');
    });


