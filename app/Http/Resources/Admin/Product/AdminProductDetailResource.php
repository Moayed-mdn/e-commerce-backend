<?php

namespace App\Http\Resources\Admin\Product;

use App\Enums\Product\ProductStatusEnum;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = app()->getLocale();
        $defaultVariant = $this->relationLoaded('variants')
            ? ($this->variants
                ->where('is_active', true)
                ->sortBy('price')
                ->first()
                ?? $this->variants->first())
            : null;
        $totalStock = $this->relationLoaded('variants')
            ? $this->variants->sum('quantity')
            : 0;
        $allImages = $this->buildImages();

        return [
            'id' => $this->id,
            'store_id' => $this->store_id,
            'available_locales' => $this->resolveAvailableLocales(),
            'default_locale' => config('content.default_locale'),
            'translations' => $this->buildTranslations($request),
            'status' => $this->is_active ? ProductStatusEnum::ACTIVE->value : ProductStatusEnum::DRAFT->value,
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
            'quantity'         => $totalStock,
            'track_quantity'   => true,
            'weight'           => $defaultVariant?->weight ?? null,
            'weight_unit'      => $defaultVariant?->weight_unit ?? null,
            'images'           => $allImages,
            'options'          => $this->buildOptions($locale),
            'variants'         => $this->relationLoaded('variants')
                ? $this->variants->map(fn($variant) => $this->formatVariant($variant))->values()
                : [],
            'category_id'      => $this->category_id,
            'brand_id'         => $this->brand_id,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }

    private function resolveAvailableLocales(): array
    {
        $configuredLocales = config('content.editable_locales', config('app.supported_locales', []));
        $translationLocales = $this->relationLoaded('translations')
            ? $this->translations->pluck('locale')->all()
            : [];

        return collect($configuredLocales)
            ->merge($translationLocales)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function buildTranslations(Request $request): array
    {
        $translations = $this->relationLoaded('translations')
            ? $this->translations->keyBy('locale')
            : collect();

        return collect($this->resolveAvailableLocales())
            ->mapWithKeys(function (string $locale) use ($request, $translations): array {
                $translation = $translations->get($locale, ['locale' => $locale]);

                return [
                    $locale => (new AdminProductTranslationResource($translation))->toArray($request),
                ];
            })
            ->all();
    }

    private function buildImages(): array
    {
        if (!$this->relationLoaded('variants')) {
            return [];
        }

        return $this->variants->flatMap(function ($variant) {
            if (!$variant->relationLoaded('images')) {
                return [];
            }

            return $variant->images->map(fn($image) => [
                'id' => $image->id,
                'url' => asset($image->image_url),
                'alt' => $image->alt_text ?? null,
                'position' => $image->position ?? 0,
            ]);
        })->values()->all();
    }

    private function buildOptions(string $locale): array
    {
        if (!$this->relationLoaded('variants')) {
            return [];
        }

        $attributeMap = collect();

        foreach ($this->variants as $variant) {
            if (!$variant->relationLoaded('attributeValues')) {
                continue;
            }

            foreach ($variant->attributeValues as $attrValue) {
                $attribute = $attrValue->attribute;
                if (!$attribute) {
                    continue;
                }

                if (!$attributeMap->has($attribute->id)) {
                    $attributeMap->put($attribute->id, [
                        'id'         => $attribute->id,
                        'code'       => $attribute->code,
                        'name'       => $attribute->translation($locale)?->name ?? $attribute->code,
                        'type'       => $attribute->type->value,
                        'sort_order' => $attribute->sort_order ?? 0,
                        'values'     => collect(),
                    ]);
                }

                $existingValues = $attributeMap->get($attribute->id)['values'];
                if (!$existingValues->contains('id', $attrValue->id)) {
                    $existingValues->push([
                        'id'    => $attrValue->id,
                        'code'  => $attrValue->code,
                        'label' => $attrValue->translation($locale)?->label ?? $attrValue->code,
                    ]);
                }
            }
        }

        return $attributeMap
            ->sortBy('sort_order')
            ->values()
            ->map(fn($option) => [
                'id'     => $option['id'],
                'code'   => $option['code'],
                'name'   => $option['name'],
                'type'   => $option['type'],
                'values' => $option['values']->values(),
            ])
            ->toArray();
    }

    private function formatVariant(ProductVariant $variant): array
    {
        $attributes = $variant->relationLoaded('attributeValues')
            ? VariantAttributeResource::collection($variant->attributeValues)
                ->resolve()
            : [];

        return [
            'id'               => $variant->id,
            'sku'              => $variant->sku,
            'price'            => (float) $variant->price,
            'quantity'         => $variant->quantity,
            'is_active'        => $variant->is_active,
            'manufacture_date' => $variant->manufacture_date,
            'expiry_date'      => $variant->expiry_date,
            'attributes'       => $attributes,
        ];
    }
}
