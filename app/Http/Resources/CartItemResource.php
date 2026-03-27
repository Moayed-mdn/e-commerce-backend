<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request)
    {
        $variant = $this->productVariant;
        $product = $variant->product;
        $locale = app()->getLocale();

        // ── Translated product name & slug ──
        $translation = $product->translations->where('locale', $locale)->first()
            ?? $product->translations->first();

        $productName = $translation?->name ?? '';
        $productSlug = $translation?->slug ?? '';

        // ── Variant primary image ──
        $primaryImage = $variant->images->where('is_primary', true)->first()
            ?? $variant->images->first();
        $imageUrl = $primaryImage ? $primaryImage->full_url : null;

        // ── Translated attributes ──
        $attributes = $variant->attributeValues->map(function ($attrValue) use ($locale) {
            // Attribute name: "Color" / "اللون"
            $attrName = $attrValue->attribute->translations
                ->where('locale', $locale)->first()?->name
                ?? $attrValue->attribute->code;

            // Attribute value: "Red" / "أحمر"
            $valueLabel = $attrValue->translations
                ->where('locale', $locale)->first()?->label
                ?? $attrValue->code;

            return [
                'name'  => $attrName,
                'value' => $valueLabel,
            ];
        });

        return [
            'id'           => $this->id,
            'quantity'     => $this->quantity,

            // ── Top-level fields (what your Nuxt CartItem interface expects) ──
            'name'         => $productName,
            'image'        => $imageUrl,
            'price'        => $variant->price,
            'max_quantity' => $variant->quantity,

            // ── Nested objects (for getItemByProductId + product page link) ──
            'product' => [
                'id'   => $product->id,
                'name' => $productName,
                'slug' => $productSlug,
            ],

            'variant' => [
                'id'         => $variant->id,
                'sku'        => $variant->sku,
                'price'      => $variant->price,
                'stock'      => $variant->quantity,
                'image'      => $imageUrl,
                'attributes' => $attributes,
            ],

            'subtotal' => round($this->quantity * $variant->price, 2),
        ];
    }
}