<?php

namespace App\Services;

use App\DTOs\Product\BestSellerDTO;
use App\DTOs\Product\ProductCardDTO;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use Carbon\Carbon;

class BestSellerService
{
    protected $cacheTtl;

    public function __construct()
    {
        $this->cacheTtl = 60 * 60 * 24;
    }

    public function buildDescendantMap(): array
    {
        $categories = Category::select('id', 'parent_id')->get();

        $childrenMap = [];
        foreach ($categories as $cat) {
            $parent = $cat->parent_id ?? 0;
            $childrenMap[$parent][] = $cat->id;
            if (!isset($childrenMap[$cat->id])) {
                $childrenMap[$cat->id] = $childrenMap[$cat->id] ?? [];
            }
        }

        $result = [];
        $allCategoryIds = $categories->pluck('id')->all();

        foreach ($allCategoryIds as $catId) {
            $stack = [$catId];
            $collected = [$catId];

            while (!empty($stack)) {
                $current = array_pop($stack);

                if (!empty($childrenMap[$current])) {
                    foreach ($childrenMap[$current] as $childId) {
                        if (!in_array($childId, $collected, true)) {
                            $collected[] = $childId;
                            $stack[] = $childId;
                        }
                    }
                }
            }

            $result[$catId] = $collected;
        }

        return $result;
    }

    public function computeAllProductSales(string $locale = 'en')
    {
        $sales = DB::table('order_items')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->join('product_translations', function ($join) use ($locale) {
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.locale', '=', $locale);
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
                'product_translations.slug as product_slug',       // ✅ from translations
                'products.category_id',
                'product_translations.description as product_description',
                'product_translations.name as product_name',       // ✅ from translations
                'display_v.id as product_variant_id',
                'display_v.price as variant_price',
                'images.image_url as variant_image',
                'images.alt_text as alt_text',
                DB::raw('SUM(order_items.quantity) as total_sold')
            )
            ->groupBy(
                'products.id',
                'product_translations.slug',
                'products.category_id',
                'product_translations.name',
                'product_translations.description',
                'display_v.id',
                'display_v.price',
                'images.image_url',
                'images.alt_text'
            )
            ->get();

        return $sales->map(function ($r) {
            $r->total_sold = (int) $r->total_sold;
            return $r;
        });
    }

    public function buildBestSellersForAllParents(int $limit = 20, bool $useCache = true): array
    {
        $locale = app()->getLocale() ?: 'en';

        $descendantsMap = $this->buildDescendantMap();
        $sales = $this->computeAllProductSales($locale);
        $salesByCategory = $sales->groupBy('category_id');

        $parents = Category::whereNull('parent_id')
            ->leftJoin('category_translations', function ($join) use ($locale) {
                $join->on('category_translations.category_id', '=', 'categories.id')
                    ->where('category_translations.locale', '=', $locale);
            })
            ->select('categories.id', 'category_translations.name', 'categories.slug')
            ->get();

        $payload = [
            'generated_at' => Carbon::now()->toIso8601String(),
            'data' => []
        ];

        $dtos = [];   // ← FIX: initialize $dtos before the loop

        foreach ($parents as $parent) {
            $parentId = $parent->id;
            $descIds = $descendantsMap[$parentId] ?? [$parentId];

            $col = collect();

            foreach ($descIds as $cid) {
                if (isset($salesByCategory[$cid])) {
                    $col = $col->concat($salesByCategory[$cid]);
                }
            }

            if ($col->isEmpty()) {
                continue;
            }

            $top = $col->sortByDesc('total_sold')
                ->values()
                ->take($limit)
                ->map(function ($r) {
                    return new ProductCardDTO(
                        product_id: (int) $r->product_id,
                        product_variant_id: (int) $r->product_variant_id,
                        slug: $r->product_slug,
                        category_id: (int) $r->category_id,
                        primary_image: $r->variant_image,
                        alt_text: $r->alt_text,
                        product_name: $r->product_name,
                        price: $r->variant_price,
                        description: $r->product_description,
                        total_sold: (int) $r->total_sold
                    );
                })
                ->values()
                ->all();

            $dtos[] = new BestSellerDTO(
                category_id: (int) $parent->id,
                category_name: $parent->name,
                category_slug: $parent->slug,
                products: $top
            );

            if ($useCache) {
                Cache::store('redis')->put("best_sellers:category:{$parentId}", $top, $this->cacheTtl);
            }
        }

        if ($useCache) {
            Cache::store('redis')->put('best_sellers:all_parents', $payload, $this->cacheTtl);
        }

        return $dtos;
    }

    public function getCachedAllParents(int $limit = 20)
    {
        return $this->buildBestSellersForAllParents($limit, true);
    }

    public function getCachedForParentId(int $parentId, int $limit = 20)
    {
        // $cached = Cache::store('redis')->get("best_sellers:category:{$parentId}");
        // if ($cached !== null) {
        //     return $cached;
        // }

        $all = $this->buildBestSellersForAllParents($limit, true);
        $category = Category::find($parentId);
        if (!$category) return [];
        return $all[$category->name] ?? [];
    }

    // ══════════════════════════════════════════════════════════
    //  REMOVED: The loose query at the bottom that referenced
    //  products.slug — it was the source of the error.
    //  If you need it for debugging, here's the fixed version:
    // ══════════════════════════════════════════════════════════
    //
    //  $locale = app()->getLocale();
    //  $uniqueSoldProducts = DB::table('order_items')
    //      ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
    //      ->join('products', 'product_variants.product_id', '=', 'products.id')
    //      ->join('product_translations', function ($join) use ($locale) {
    //          $join->on('products.id', '=', 'product_translations.product_id')
    //              ->where('product_translations.locale', '=', $locale);
    //      })
    //      ->select(
    //          'product_translations.name as product_name',   // ← was products.slug
    //          'product_variants.price as variant_price'
    //      )
    //      ->groupBy(
    //          'product_translations.name',                   // ← was products.slug
    //          'product_variants.price'
    //      )
    //      ->get();
}