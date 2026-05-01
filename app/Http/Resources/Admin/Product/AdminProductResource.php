<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category->name ?? null,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}
