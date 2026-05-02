<?php

namespace App\Http\Resources\Admin\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'order_number'    => $this->order_number,
            'status'          => $this->status,
            'payment_status'  => $this->payment_status,
            'subtotal'        => (float) $this->subtotal,
            'tax_amount'      => (float) $this->tax_amount,
            'shipping_amount' => (float) $this->shipping_amount,
            'discount_amount' => (float) $this->discount_amount,
            'total'           => (float) $this->total,
            'user'            => $this->whenLoaded('user', fn() => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ]),
            'items'           => $this->whenLoaded('items', fn() =>
                $this->items->map(fn($item) => [
                    'id'           => $item->id,
                    'product_name' => $item->product_name,
                    'quantity'     => $item->quantity,
                    'unit_price'   => (float) $item->unit_price,
                    'subtotal'     => round(
                        (float) $item->unit_price * $item->quantity, 2
                    ),
                ])
            ),
            'created_at'      => $this->created_at,
        ];
    }
}
