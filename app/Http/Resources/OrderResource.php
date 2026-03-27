<?php
// app/Http/Resources/OrderResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'order_number'          => $this->order_number,
            'status'                => $this->status,
            'payment_status'        => $this->payment_status,

            'subtotal'              => (float) $this->subtotal,
            'tax_amount'            => (float) $this->tax_amount,
            'shipping_amount'       => (float) $this->shipping_amount,
            'discount_amount'       => (float) $this->discount_amount,
            'total'                 => (float) $this->total,
            'currency'              => $this->currency ?? 'usd',

            'shipping_method'       => $this->shipping_method,
            'tracking_number'       => $this->tracking_number,
            'shipping_address_data' => $this->shipping_address_data,

            'can_cancel'            => $this->canBeCancelled(),

            'items_count'           => $this->whenLoaded('items', fn() => $this->items->sum('quantity')),
            'items'                 => OrderItemResource::collection($this->whenLoaded('items')),

            'shipped_at'            => $this->shipped_at?->toISOString(),
            'delivered_at'          => $this->delivered_at?->toISOString(),
            'created_at'            => $this->created_at->toISOString(),
        ];
    }
}