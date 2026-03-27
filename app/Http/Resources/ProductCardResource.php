<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'product_variant_id' => $this->product_variant_id,
            'slug' => $this->slug,
            'category_id' => $this->category_id,
            'primary_image' => asset($this->primary_image),
            'alt_text' => $this->alt_text,
            'product_name' => $this->product_name,
            'price' => $this->price,
            'description' => $this->description,
            'total_sold' => $this->total_sold,
        ];
    }
}