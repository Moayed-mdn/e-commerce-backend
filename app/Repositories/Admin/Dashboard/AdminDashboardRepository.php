<?php

namespace App\Repositories\Admin\Dashboard;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AdminDashboardRepository
{
    public function getTotalRevenue(int $storeId): float
    {
        return (float) Order::where('store_id', $storeId)
            ->sum('total');
    }

    public function getTotalOrders(int $storeId): int
    {
        return Order::where('store_id', $storeId)->count();
    }

    public function getTotalCustomers(int $storeId): int
    {
        return Order::where('store_id', $storeId)
            ->distinct('user_id')
            ->count('user_id');
    }

    public function getOrdersThisMonth(int $storeId): int
    {
        return Order::where('store_id', $storeId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function getOrdersLastMonth(int $storeId): int
    {
        return Order::where('store_id', $storeId)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function getRevenueThisMonth(int $storeId): float
    {
        return (float) Order::where('store_id', $storeId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
    }

    public function getRevenueLastMonth(int $storeId): float
    {
        return (float) Order::where('store_id', $storeId)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
    }

    public function getRecentOrders(int $storeId, int $limit = 10): Collection
    {
        return Order::where('store_id', $storeId)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getTopProducts(int $storeId, int $limit = 10): Collection
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('products.store_id', $storeId)
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get();
    }
}
