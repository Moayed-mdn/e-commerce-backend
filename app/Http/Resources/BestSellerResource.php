<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductCardResource;

class BestSellerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'category_id' => $this->resource->category_id,
            'category_name' => $this->category_name,
            'category_slug' => $this->category_slug,
            'products' => ProductCardResource::collection($this->resource->products),
        ];
    }
}