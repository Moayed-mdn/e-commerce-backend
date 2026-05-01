<?php

namespace App\Http\Resources\Admin\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'total' => $this->total,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'items' => $this->items->map(fn($item) => [
                'id' => $item->id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->quantity * $item->price,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
