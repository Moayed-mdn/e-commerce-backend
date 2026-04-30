<?php

declare(strict_types=1);

namespace App\Repositories\OrderItem;

use App\Models\OrderItem;

class OrderItemRepository
{
    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function createMany(array $items): void
    {
        foreach ($items as $item) {
            OrderItem::create($item);
        }
    }

    public function create(array $data): OrderItem
    {
        return OrderItem::create($data);
    }
}
