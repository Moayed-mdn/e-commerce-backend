<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'is_active' => $this->is_active,
            'variants' => $this->variants->map(fn($v) => [
                'id' => $v->id,
                'sku' => $v->sku,
                'price' => $v->price,
                'quantity' => $v->quantity,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
