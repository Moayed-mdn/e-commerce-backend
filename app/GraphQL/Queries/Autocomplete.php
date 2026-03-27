<?php
// app/GraphQL/Queries/Autocomplete.php

namespace App\GraphQL\Queries;

use App\Models\Brand;
use App\Models\CategoryTranslation;
use App\Models\Product;
use App\Models\ProductTranslation;

class Autocomplete
{
    public function __invoke($rootValue, array $args): array
    {
        $query  = trim($args['query']);
        $locale = $args['locale'] ?? 'en';
        $limit  = min($args['limit'] ?? 10, 20);

        if (mb_strlen($query) < 2) {
            return [];
        }

        $term = str_replace(['%', '_', '\\'], ['\\%', '\\_', '\\\\'], $query);
        $suggestions = collect();

        // ── Products (60% of slots) ────────────────────────────

        $productLimit = (int) ceil($limit * 0.6);

        $translations = ProductTranslation::where('locale', $locale)
            ->where('name', 'LIKE', "%{$term}%")
            ->whereHas('product', fn ($q) => $q->where('is_active', true))
            ->orderByRaw("
                CASE
                    WHEN name = ? THEN 1
                    WHEN name LIKE ? THEN 2
                    ELSE 3
                END
            ", [$query, "{$query}%"])
            ->limit($productLimit)
            ->get()
            ->pluck('product_id');

        if ($translations->isNotEmpty()) {
            $products = Product::whereIn('id', $translations)
                ->with([
                    'translations',
                    'defaultVariant.images',
                    'variants' => fn ($q) => $q->where('is_active', true)->with('images'),
                    'approvedReviews',
                ])
                ->get();

            foreach ($products as $product) {
                $translation = $product->translation($locale);
                $variant     = $product->display_variant;
                $imageUrl    = $this->resolveImage($product);

                $suggestions->push([
                    'id'            => (string) $product->id,
                    'text'          => $translation?->name ?? '',
                    'type'          => 'PRODUCT',
                    'slug'          => $translation?->slug ?? '',
                    'image_url'     => $imageUrl,
                    'price'         => $variant?->price,
                    'avg_rating'    => $product->avg_rating,
                    'reviews_count' => $product->reviews_count,
                ]);
            }
        }

        // ── Categories (25% of slots) ──────────────────────────

        $catLimit = (int) ceil($limit * 0.25);

        $categories = CategoryTranslation::where('locale', $locale)
            ->where('name', 'LIKE', "%{$term}%")
            ->orderByRaw("
                CASE
                    WHEN name = ? THEN 1
                    WHEN name LIKE ? THEN 2
                    ELSE 3
                END
            ", [$query, "{$query}%"])
            ->limit($catLimit)
            ->get()
            ->map(fn (CategoryTranslation $t) => [
                'id'            => (string) $t->category_id,
                'text'          => $t->name,
                'type'          => 'CATEGORY',
                'slug'          => $t->slug,
                'image_url'     => null,
                'price'         => null,
                'avg_rating'    => null,
                'reviews_count' => null,
            ]);

        $suggestions = $suggestions->merge($categories);

        // ── Brands (15% of slots) ──────────────────────────────

        $brandLimit = (int) ceil($limit * 0.15);

        $brands = Brand::where('is_active', true)
            ->where('name', 'LIKE', "%{$term}%")
            ->orderByRaw("
                CASE
                    WHEN name = ? THEN 1
                    WHEN name LIKE ? THEN 2
                    ELSE 3
                END
            ", [$query, "{$query}%"])
            ->limit($brandLimit)
            ->get()
            ->map(fn (Brand $b) => [
                'id'            => (string) $b->id,
                'text'          => $b->name,
                'type'          => 'BRAND',
                'slug'          => $b->slug,
                'image_url'     => $b->logo_url,
                'price'         => null,
                'avg_rating'    => null,
                'reviews_count' => null,
            ]);

        $suggestions = $suggestions->merge($brands);

        return $suggestions->take($limit)->values()->all();
    }

    private function resolveImage(Product $product): ?string
    {
        $variant = $product->defaultVariant;

        if ($variant && $variant->relationLoaded('images')) {
            $img = $variant->images->where('is_primary', true)->first();
            if ($img) return $img->full_url;
        }

        if ($product->relationLoaded('variants')) {
            foreach ($product->variants as $v) {
                if (!$v->is_active) continue;
                if ($v->relationLoaded('images')) {
                    $img = $v->images->where('is_primary', true)->first();
                    if ($img) return $img->full_url;
                }
            }
        }

        return null;
    }
}