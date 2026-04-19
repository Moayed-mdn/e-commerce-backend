<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordResetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SocialAuthController;
Route::name('v1.users.auth.')
    ->prefix('/v1/users/auth')
    ->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
        
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
        Route::post('/password/forgot', [PasswordResetController::class, 'sendResetLink'])->name('password.forgot');
        Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])->name('email.resend')->middleware('throttle:verification-resend');
        Route::get('/google/redirect', [SocialAuthController::class, 'redirect']);
        Route::get('/google/callback', [SocialAuthController::class, 'callback']);
        Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum')->name('me');
    });