<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductService
{
    /**
     * Build the base product query with joins for listing.
     */
    public function buildBaseProductQuery()
    {
        $locale = app()->getLocale();

        return Product::active()
            ->leftJoin('product_translations', function ($join) use ($locale) {
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.locale', $locale);
            })
            ->leftJoin('product_variants as display_v', function ($join) {
                $join->on('display_v.id', '=', DB::raw("(
                    SELECT id FROM product_variants 
                    WHERE product_id = products.id 
                    ORDER BY (CASE WHEN id = products.product_variant_id THEN 0 ELSE 1 END), id ASC 
                    LIMIT 1
                )"));
            })
            ->leftJoin('images', function ($join) {
                $join->on('display_v.id', '=', 'images.imageable_id')
                    ->where('images.imageable_type', '=', 'App\\Models\\ProductVariant')
                    ->where('images.is_primary', '=', true);
            })
            ->select(
                'products.id as product_id',
                'products.category_id',
                'display_v.id as product_variant_id',
                'product_translations.slug as slug',
                'product_translations.name as product_name',
                'product_translations.description as description',
                'display_v.price as price',
                'images.image_url as primary_image'
            );
    }

    /**
     * Get filter ranges (min/max price, manufacture/expiry dates) for a set of products.
     */
    public function getProductFilterRanges($query): object
    {
        $filterQuery = clone $query;
        $productIdsSub = $filterQuery->select('products.id');

        return DB::table('product_variants')
            ->whereIn('product_id', $productIdsSub)
            ->selectRaw("
                MIN(price) AS min_price,
                MAX(price) AS max_price,
                MIN(manufacture_date) AS earliest_manufacture,
                MAX(expiry_date) AS latest_expiry
            ")->first();
    }

    /**
     * Apply price and date filters to a query based on request parameters.
     */
    public function applyFilters($query, Request $request)
    {
        if ($request->filled('min_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        if ($request->filled('earliest_manufacture')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('manufacture_date', '>=', $request->earliest_manufacture);
            });
        }

        if ($request->filled('latest_expiry')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('expiry_date', '>=', $request->latest_expiry);
            });
        }

        return $query;
    }

    /**
     * Get category descendants for filters.
     */
    public function getCategoryDescendants(): array
    {
        $locale = app()->getLocale();

        return Category::whereNull('parent_id')
            ->leftJoin('category_translations', function ($join) use ($locale) {
                $join->on('categories.id', '=', 'category_translations.category_id')
                    ->where('category_translations.locale', $locale);
            })
            ->select('categories.id', 'category_translations.slug as slug', 'category_translations.name as name')
            ->get()
            ->toArray();
    }

    /**
     * Find a category by its localized slug.
     */
    public function findCategoryBySlug(string $slug): ?Category
    {
        return Category::findByLocalizedSlug($slug);
    }

    /**
     * Find a category by its localized slug or fail.
     */
    public function findCategoryBySlugOrFail(string $slug): Category
    {
        return Category::findByLocalizedSlugOrFail($slug);
    }

    /**
     * Flatten category descendants with translations.
     */
    public function flattenCategoryDescendants(Category $category, string $locale): array
    {
        $result = collect();
        $this->flattenDescendantsRecursive($category, $result, $locale);
        return $result->toArray();
    }

    /**
     * Recursively flatten category descendants.
     */
    private function flattenDescendantsRecursive(Category $category, &$result, string $locale): void
    {
        foreach ($category->children as $child) {
            $translation = $child->translation($locale);

            $result->push([
                'id'   => $child->id,
                'name' => $translation?->name ?? $child->slug,
                'slug' => $translation?->slug ?? $child->slug,
            ]);

            if ($child->relationLoaded('descendants') || $child->relationLoaded('children')) {
                $this->flattenDescendantsRecursive($child, $result, $locale);
            }
        }
    }

    /**
     * Find a product by its localized slug.
     */
    public function findProductBySlug(string $slug): ?Product
    {
        return Product::findBySlug($slug);
    }

    /**
     * Find a product by its localized slug or fail.
     */
    public function findProductBySlugOrFail(string $slug): Product
    {
        return Product::findBySlugOrFail($slug);
    }

    /**
     * Get related products for a given product.
     */
    public function getRelatedProducts(Product $currentProduct, int $limit = 8): \Illuminate\Support\Collection
    {
        $currentProduct->load(['category', 'tags']);

        $relatedQuery = Product::with([
            'translations',
            'activeVariants.images',
            'defaultVariant.images',
        ])
            ->where('id', '!=', $currentProduct->id)
            ->where('is_active', true);

        if ($currentProduct->category_id) {
            $relatedQuery->where('category_id', $currentProduct->category_id);
        }

        if ($currentProduct->tags->isNotEmpty()) {
            $tagIds = $currentProduct->tags->pluck('id');
            $relatedQuery->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('product_tags.id', $tagIds);
            });
        }

        $relatedProducts = $relatedQuery
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        if ($relatedProducts->count() < 4) {
            $additionalProducts = Product::with([
                'translations',
                'activeVariants.images',
                'defaultVariant.images',
            ])
                ->where('id', '!=', $currentProduct->id)
                ->where('is_active', true)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->inRandomOrder()
                ->limit($limit - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->merge($additionalProducts);
        }

        return $relatedProducts;
    }
}
