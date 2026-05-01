<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\User\AdminUserController;
use App\Http\Controllers\Api\Admin\Product\AdminProductController;
use App\Http\Controllers\Api\Admin\Order\AdminOrderController;
use App\Http\Controllers\Api\Admin\Dashboard\AdminDashboardController;
use App\Enums\PermissionEnum;

Route::prefix('v1/admin/stores/{store}')
    ->middleware(['auth:sanctum', 'store.context'])
    ->group(function () {

        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->middleware('permission:' . PermissionEnum::USER_VIEW);
            Route::get('/{user}', [AdminUserController::class, 'show'])->middleware('permission:' . PermissionEnum::USER_VIEW);
            Route::patch('/{user}/block', [AdminUserController::class, 'block'])->middleware('permission:' . PermissionEnum::USER_BLOCK);
            Route::patch('/{user}/unblock', [AdminUserController::class, 'unblock'])->middleware('permission:' . PermissionEnum::USER_BLOCK);
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->middleware('permission:' . PermissionEnum::USER_DELETE);
            Route::patch('/{user}/restore', [AdminUserController::class, 'restore'])->middleware('permission:' . PermissionEnum::USER_RESTORE);
        });

        // Product Management
        Route::prefix('products')->group(function () {
            Route::get('/', [AdminProductController::class, 'index'])->middleware('permission:' . PermissionEnum::PRODUCT_VIEW);
            Route::get('/{product}', [AdminProductController::class, 'show'])->middleware('permission:' . PermissionEnum::PRODUCT_VIEW);
            Route::post('/', [AdminProductController::class, 'store'])->middleware('permission:' . PermissionEnum::PRODUCT_CREATE);
            Route::patch('/{product}', [AdminProductController::class, 'update'])->middleware('permission:' . PermissionEnum::PRODUCT_UPDATE);
            Route::delete('/{product}', [AdminProductController::class, 'destroy'])->middleware('permission:' . PermissionEnum::PRODUCT_DELETE);
            Route::patch('/{product}/restore', [AdminProductController::class, 'restore'])->middleware('permission:' . PermissionEnum::PRODUCT_RESTORE);
        });

        // Order Management
        Route::prefix('orders')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->middleware('permission:' . PermissionEnum::ORDER_VIEW);
            Route::get('/{order}', [AdminOrderController::class, 'show'])->middleware('permission:' . PermissionEnum::ORDER_VIEW);
            Route::patch('/{order}/status', [AdminOrderController::class, 'updateStatus'])->middleware('permission:' . PermissionEnum::ORDER_UPDATE_STATUS);
            Route::patch('/{order}/cancel', [AdminOrderController::class, 'cancel'])->middleware('permission:' . PermissionEnum::ORDER_CANCEL);
            Route::patch('/{order}/refund', [AdminOrderController::class, 'refund'])->middleware('permission:' . PermissionEnum::ORDER_REFUND);
        });

        // Dashboard
        Route::prefix('dashboard')->group(function () {
            Route::get('/stats', [AdminDashboardController::class, 'stats'])->middleware('permission:' . PermissionEnum::DASHBOARD_VIEW);
            Route::get('/recent-orders', [AdminDashboardController::class, 'recentOrders'])->middleware('permission:' . PermissionEnum::DASHBOARD_VIEW);
            Route::get('/top-products', [AdminDashboardController::class, 'topProducts'])->middleware('permission:' . PermissionEnum::DASHBOARD_VIEW);
        });
    });
