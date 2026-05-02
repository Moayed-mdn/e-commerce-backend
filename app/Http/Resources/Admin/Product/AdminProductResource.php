<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductResource extends JsonResource
{
    public function toArray($request): array
    {
        $defaultVariant = $this->whenLoaded('variants',
            fn() => $this->variants
                ->where('is_active', true)
                ->sortBy('price')
                ->first()
        );

        $totalStock = $this->whenLoaded('variants',
            fn() => $this->variants->sum('quantity')
        );

        $primaryImage = $this->whenLoaded('variants', function () {
            foreach ($this->variants as $variant) {
                if ($variant->relationLoaded('images')) {
                    $img = $variant->images
                        ->where('is_primary', true)
                        ->first()
                        ?? $variant->images->first();
                    if ($img) return $img;
                }
            }
            return null;
        });

        return [
            'id'               => $this->id,
            'store_id'         => $this->store_id,
            'name'             => $this->name,
            'slug'             => $this->slug ?? '',
            'status'           => $this->is_active ? 'active' : 'draft',
            'price'            => $defaultVariant
                ? (float) $defaultVariant->price
                : 0,
            'sku'              => $defaultVariant?->sku ?? null,
            'quantity'         => $totalStock ?? 0,
            'images'           => $primaryImage ? [[
                'id'       => $primaryImage->id,
                'url'      => asset($primaryImage->image_url),
                'alt'      => $primaryImage->alt_text ?? null,
                'position' => $primaryImage->position ?? 0,
            ]] : [],
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
