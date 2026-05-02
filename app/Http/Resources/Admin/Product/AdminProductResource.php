<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'category'       => $this->whenLoaded('category',
                fn() => $this->category?->name
            ),
            'is_active'      => $this->is_active,
            'variants_count' => $this->whenLoaded('variants',
                fn() => $this->variants->count()
            ),
            'price_from'     => $this->whenLoaded('variants',
                fn() => $this->variants->min('price')
            ),
            'created_at'     => $this->created_at,
        ];
    }
}
