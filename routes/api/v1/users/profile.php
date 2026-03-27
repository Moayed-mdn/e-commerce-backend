<?php

// routes/api.php — inside your authenticated v1/users group

use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

// These need auth:sanctum middleware
Route::middleware('auth:sanctum')
    ->prefix('/v1/users/profile')
    ->controller(ProfileController::class)->group(function () {
    Route::get('/', 'show');
    Route::put('/info','updateInfo');
    Route::put('/password','updatePassword');
    Route::post('/avatar', 'updateAvatar');
    Route::delete('/', 'destroy');
});