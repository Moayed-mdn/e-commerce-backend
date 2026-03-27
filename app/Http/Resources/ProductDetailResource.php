<?php
// app/Http/Resources/ProductDetailResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray($request)
    {
        $locale = app()->getLocale();
        $translation = $this->translation($locale);

        return [
            'id'                 => $this->id,
            'name'               => $translation?->name ?? '',
            'slug'               => $translation?->slug ?? '',
            'description'        => $translation?->description ?? '',
            'default_variant_id' => $this->product_variant_id,

            'category' => $this->whenLoaded('category', function () use ($locale) {
                $catTranslation = $this->category->translations
                    ->where('locale', $locale)->first()
                    ?? $this->category->translations->first();

                return [
                    'id'   => $this->category->id,
                    'name' => $catTranslation?->name ?? '',
                    'slug' => $catTranslation?->slug ?? '',
                ];
            }),

            'brand' => $this->whenLoaded('brand', function () {
                if (!$this->brand) return null;
                return [
                    'id'   => $this->brand->id,
                    'name' => $this->brand->name,
                ];
            }),

            'attributes' => $this->buildUniqueAttributes($locale),

            'variants' => $this->whenLoaded('activeVariants', function () use ($locale) {
                return $this->activeVariants->map(function ($variant) use ($locale) {
                    return $this->formatVariant($variant, $locale);
                });
            }),
        ];
    }

    /**
     * Build unique attributes for the variant selector.
     * { "Color": ["Red", "Blue"], "Size": ["500g", "1kg"] }
     */
    private function buildUniqueAttributes(string $locale): array
    {
        if (!$this->relationLoaded('activeVariants')) {
            return [];
        }

        return $this->activeVariants
            ->flatMap->attributeValues
            ->map(function ($attrValue) use ($locale) {
                $name = $attrValue->attribute->translations
                    ->where('locale', $locale)->first()?->name
                    ?? $attrValue->attribute->code;

                $value = $attrValue->translations
                    ->where('locale', $locale)->first()?->label
                    ?? $attrValue->code;

                return ['name' => $name, 'value' => $value];
            })
            ->groupBy('name')
            ->map(fn($items) => $items->pluck('value')->unique()->values())
            ->toArray();
    }

    /**
     * Format a single variant for the response.
     */
    private function formatVariant($variant, string $locale): array
    {
        // Attributes as array: [{ name: "Color", value: "Red" }]
        $attributes = $variant->attributeValues->map(function ($attrValue) use ($locale) {
            $name = $attrValue->attribute->translations
                ->where('locale', $locale)->first()?->name
                ?? $attrValue->attribute->code;

            $value = $attrValue->translations
                ->where('locale', $locale)->first()?->label
                ?? $attrValue->code;

            return ['name' => $name, 'value' => $value];
        })->values()->toArray();

        // Attribute map for variant matching: { "Color": "Red", "Size": "500g" }
        $attributeMap = collect($attributes)->pluck('value', 'name')->toArray();

        // Images
        $images = $variant->images->map(fn($img) => [
            'id'         => $img->id,
            'url'        => asset($img->image_url),
            'alt_text'   => $img->alt_text,
            'is_primary' => $img->is_primary,
        ])->values()->toArray();

        return [
            'id'               => $variant->id,
            'sku'              => $variant->sku,
            'price'            => (float) $variant->price,
            'stock'            => $variant->quantity,
            'is_active'        => $variant->is_active,
            'manufacture_date' => $variant->manufacture_date,
            'expiry_date'      => $variant->expiry_date,
            'attributes'       => $attributes,
            'attribute_map'    => $attributeMap,
            'images'           => $images,
            'primary_image'    => collect($images)->firstWhere('is_primary', true)
                ?? collect($images)->first(),
        ];
    }
}