<?php

namespace App\Repositories\Admin\Order;

use App\Exceptions\Order\OrderNotFoundException;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminOrderRepository
{
    /**
     * List orders for a specific store with pagination
     */
    public function listForStore(int $storeId, ?string $search = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::query()
            ->where('store_id', $storeId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->with(['user', 'items.product'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find an order in a specific store or throw exception
     */
    public function findInStore(int $orderId, int $storeId): Order
    {
        $order = Order::query()
            ->where('store_id', $storeId)
            ->where('id', $orderId)
            ->first();

        if (!$order) {
            throw new OrderNotFoundException();
        }

        return $order;
    }

    /**
     * Update order status
     */
    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);
        return $order->fresh();
    }
}
