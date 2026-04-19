<?php

declare(strict_types=1);

namespace App\Repositories\Order;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository
{
    public function getUserOrders(int $userId): LengthAwarePaginator
    {
        return Order::query()
            ->where('user_id', $userId)
            ->with(['items', 'shippingAddress', 'billingAddress', 'paymentMethod'])
            ->latest()
            ->paginate(10);
    }

    public function findById(int $id): ?Order
    {
        return Order::query()
            ->with([
                'items.productVariant.product.images',
                'shippingAddress',
                'billingAddress',
                'paymentMethod'
            ])
            ->find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);
        return $order->fresh();
    }

    public function cancel(Order $order): Order
    {
        $order->update([
            'status' => 'cancelled',
            'payment_status' => 'refunded'
        ]);
        return $order->fresh();
    }

    public function restoreProductVariants(Order $order): void
    {
        foreach ($order->items as $item) {
            $item->productVariant->increment('quantity', $item->quantity);
        }
    }
}
