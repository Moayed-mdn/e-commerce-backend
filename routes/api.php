<?php

use Illuminate\Support\Facades\Route;

// Auth (no store context)
require 'api/v1/users/auth.php';

// Public (no store context)
require 'api/v1/users/homepage.php';
require 'api/v1/users/category.php';

// Profile (no store context)
require 'api/v1/users/profile.php';

// Guest checkout status (no auth, no store context)
Route::prefix('/v1/users/checkout')
    ->controller(\App\Http\Controllers\Api\Payment\CheckoutController::class)
    ->withoutMiddleware(['auth:sanctum'])
    ->group(function () {
        Route::get('/status/{sessionId}', 'status');
    });

// Stripe webhook (no store context)
require 'api/v1/stripe/webhook.php';

// Store-scoped routes
require 'api/v1/stores/cart.php';
require 'api/v1/stores/orders.php';
require 'api/v1/stores/products.php';
require 'api/v1/stores/addresses.php';
require 'api/v1/stores/checkout.php';
require 'api/v1/stores/search.php';

// Store management routes (outside {store} group - POST has no store context yet)
require 'api/v1/stores/store-management.php';