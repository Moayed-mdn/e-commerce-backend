<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        $defaultVariant = $this->whenLoaded('variants',
            fn() => $this->variants
                ->where('is_active', true)
                ->sortBy('price')
                ->first()
                ?? $this->variants->first()
        );

        $totalStock = $this->whenLoaded('variants',
            fn() => $this->variants->sum('quantity')
        );

        $allImages = $this->whenLoaded('variants', function () {
            return $this->variants->flatMap(function ($variant) {
                if (!$variant->relationLoaded('images')) return [];
                return $variant->images->map(fn($img) => [
                    'id'       => $img->id,
                    'url'      => asset($img->image_url),
                    'alt'      => $img->alt_text ?? null,
                    'position' => $img->position ?? 0,
                ]);
            })->values();
        });

        return [
            'id'               => $this->id,
            'store_id'         => $this->store_id,
            'name'             => $this->name,
            'slug'             => $this->slug ?? '',
            'description'      => $this->description ?? null,
            'status'           => $this->is_active ? 'active' : 'draft',
            'price'            => $defaultVariant
                ? (float) $defaultVariant->price
                : 0,
            'compare_at_price' => $defaultVariant
                ? ($defaultVariant->compare_at_price
                    ? (float) $defaultVariant->compare_at_price
                    : null)
                : null,
            'cost_per_item'    => $defaultVariant
                ? ($defaultVariant->cost_per_item
                    ? (float) $defaultVariant->cost_per_item
                    : null)
                : null,
            'sku'              => $defaultVariant?->sku ?? null,
            'barcode'          => $defaultVariant?->barcode ?? null,
            'quantity'         => $totalStock ?? 0,
            'track_quantity'   => true,
            'weight'           => $defaultVariant?->weight ?? null,
            'weight_unit'      => $defaultVariant?->weight_unit ?? null,
            'images'           => $allImages ?? [],
            'variants'         => $this->whenLoaded('variants', fn() =>
                $this->variants->map(fn($v) => [
                    'id'               => $v->id,
                    'sku'              => $v->sku,
                    'price'            => (float) $v->price,
                    'quantity'         => $v->quantity,
                    'is_active'        => $v->is_active,
                    'manufacture_date' => $v->manufacture_date,
                    'expiry_date'      => $v->expiry_date,
                    'attributes'       => $v->relationLoaded('attributeValues')
                        ? $v->attributeValues->map(fn($av) => [
                            'name'  => $av->attribute->code ?? '',
                            'value' => $av->code ?? '',
                        ])
                        : [],
                ])
            ),
            'category_id'      => $this->category_id,
            'brand_id'         => $this->brand_id,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
