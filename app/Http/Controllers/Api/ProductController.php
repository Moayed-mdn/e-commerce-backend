<?php
// app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\RelatedProductResource;
use App\Http\Resources\ProductCardResource;
use App\Http\Resources\ProductDetailResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * List products (paginated, filterable).
     * UNCHANGED — keeping your existing index() method exactly as-is.
     */
    public function index(Request $request)
    {
        $query = Product::active()
            ->leftJoin('product_translations', function($join){
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.locale', app()->getLocale());
            })
            ->leftJoin('product_variants as display_v', function($join) {
                $join->on('display_v.id', '=', DB::raw("(
                    SELECT id FROM product_variants 
                    WHERE product_id = products.id 
                    ORDER BY (CASE WHEN id = products.product_variant_id THEN 0 ELSE 1 END), id ASC 
                    LIMIT 1
                )"));
            })
            ->leftJoin('images', function($join) {
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

        $descendants = Category::whereNull('parent_id')->leftJoin('category_translations',function($join){
            $join->on('categories.id','=','category_translations.category_id')
                ->where('category_translations.locale',app()->getLocale());
        })->select('categories.id','category_translations.slug as slug','category_translations.name as name')
            ->get();

        if($request->filled('category_slug')){
            $category = Category::whereHas('translations', function($q) use($request){
                $q->where('slug', $request->category_slug);
            })->firstOrFail();

            $_descendants = $category->descendants()->leftJoin('category_translations',function($join){
                $join->on('categories.id','=','category_translations.category_id')
                    ->where('category_translations.locale',app()->getLocale());
            })->select('categories.id','category_translations.slug as slug','category_translations.name as name');

            $descendantsWithSelf = $_descendants->pluck('id')->push($category->id);

            $query->whereHas('category',function ($query) use ($descendantsWithSelf){
                $query->whereIn('id',$descendantsWithSelf);
            });
        }

        $filterQuery = clone $query;
        $productIdsSub = $filterQuery->select('products.id');

        $variantStatus = DB::table('product_variants')
            ->whereIn('product_id',$productIdsSub)
            ->selectRaw("
                MIN(price) AS min_price,
                MAX(price) AS max_price,
                MIN(manufacture_date) AS earliest_manufacture,
                MAX(expiry_date) AS latest_expiry
            ")->first();

        if($request->filled('min_price'))
            $query->whereHas('variants',function($query) use($request) {
                $query->where('price','>=',$request->min_price);
            });

        if($request->filled('max_price'))
            $query->whereHas('variants',function($query) use($request) {
                $query->where('price','<=',$request->max_price);
            });

        if($request->filled('earliest_manufacture'))
            $query->whereHas('variants',function($query) use($request) {
                $query->where('manufacture_date','>=',$request->earliest_manufacture);
            });

        if($request->filled('latest_expiry'))
            $query->whereHas('variants',function($query) use($request) {
                $query->where('expiry_date','>=',$request->latest_expiry);
            });

        $perPage = $request->get('per_page',20);
        $products = $query->paginate($perPage);

        return response()->json([
            'data' => ProductCardResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
            ],
            'filters' => [
                'descendants' => $descendants,
                'min_price' => $variantStatus->min_price,
                'max_price' => $variantStatus->max_price,
                'earliest_manufacture' => $variantStatus->earliest_manufacture,
                'latest_expiry' => $variantStatus->latest_expiry
            ]
        ]);
    }

    /**
 * Products filtered by category (localized slug).
 *
 * GET /products/category/{slug}
 * GET /products/category/{slug}?category_slug=sub-category&min_price=10&max_price=100
 */
public function indexByCategory(string $slug, Request $request)
{
    $locale = app()->getLocale();

    // ── Find category by localized slug ──
    $category = Category::findByLocalizedSlugOrFail($slug);
    $category->loadMissing(['translations', 'descendants.translations']);

    // ── Build subcategory filter list (translated) ──
    $descendantCategories = collect();
    $this->flattenDescendants($category, $descendantCategories, $locale);

    // ── Main product query (same pattern as index()) ──
    $query = Product::active()
        ->leftJoin('product_translations', function ($join) use ($locale) {
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
            'products.category_id',
            'display_v.id as product_variant_id',
            'product_translations.slug as slug',
            'product_translations.name as product_name',
            'product_translations.description as description',
            'display_v.price as price',
            'images.image_url as primary_image',
            'images.alt_text as alt_text'
        );

    // ── Filter by sub-category slug (from query param) ──
    if ($request->filled('category_slug')) {
        $subCategory = Category::findByLocalizedSlug($request->category_slug);

        if ($subCategory) {
            $subIds = $subCategory->allDescendantIds();
            $query->whereIn('products.category_id', $subIds);
        }
    } else {
        // Default: show all products in this category + descendants
        $allIds = $category->allDescendantIds();
        $query->whereIn('products.category_id', $allIds);
    }

    // ── Build filter ranges (before applying price/date filters) ──
    $filterQuery = clone $query;
    $productIdsSub = $filterQuery->select('products.id');

    $variantStatus = DB::table('product_variants')
        ->whereIn('product_id', $productIdsSub)
        ->selectRaw("
            MIN(price) AS min_price,
            MAX(price) AS max_price,
            MIN(manufacture_date) AS earliest_manufacture,
            MAX(expiry_date) AS latest_expiry
        ")->first();

    // ── Apply price/date filters ──
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

    // ── Paginate ──
    $perPage = $request->get('per_page', 20);
    $products = $query->paginate($perPage);

    // ── Category translation for the response ──
    $categoryTranslation = $category->translation($locale);

    return response()->json([
        'data' => ProductCardResource::collection($products->items()),
        'category' => [
            'id'         => $category->id,
            'name'       => $categoryTranslation?->name ?? $category->slug,
            'slug'       => $categoryTranslation?->slug ?? $category->slug,
            'breadcrumb' => $category->breadcrumb,
        ],
        'pagination' => [
            'current_page' => $products->currentPage(),
            'total'        => $products->total(),
            'per_page'     => $products->perPage(),
            'last_page'    => $products->lastPage(),
        ],
        'filters' => [
            'descendants'          => $descendantCategories,
            'min_price'            => $variantStatus->min_price ?? null,
            'max_price'            => $variantStatus->max_price ?? null,
            'earliest_manufacture' => $variantStatus->earliest_manufacture ?? null,
            'latest_expiry'        => $variantStatus->latest_expiry ?? null,
        ],
    ]);
}

/**
 * Recursively flatten descendants with translated name/slug.
 */
private function flattenDescendants(Category $category, \Illuminate\Support\Collection &$result, string $locale): void
{
    foreach ($category->children as $child) {
        $translation = $child->translation($locale);

        $result->push([
            'id'   => $child->id,
            'name' => $translation?->name ?? $child->slug,
            'slug' => $translation?->slug ?? $child->slug,
        ]);

        if ($child->relationLoaded('descendants') || $child->relationLoaded('children')) {
            $this->flattenDescendants($child, $result, $locale);
        }
    }
}

    // ═══════════════════════════════════════════════════════════
    //  UPDATED METHODS BELOW
    // ═══════════════════════════════════════════════════════════

    /**
     * Show product detail by localized slug.
     *
     * GET /products/{slug}
     */
    public function show(string $slug)
    {
        $product = Product::findBySlugOrFail($slug);

        $product->load([
            'translations',
            'category.translations',
            'brand',
            'activeVariants.attributeValues.translations',
            'activeVariants.attributeValues.attribute.translations',
            'activeVariants.images',
        ]);

        return response()->json([
            'data' => new ProductDetailResource($product),
        ]);
    }

    /**
     * Get related products by localized slug.
     *
     * GET /products/{slug}/related
     */
    public function related(string $slug)
    {
        $currentProduct = Product::findBySlugOrFail($slug);
        $currentProduct->load(['category', 'tags']);

        try {
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
                ->limit(8)
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
                    ->limit(8 - $relatedProducts->count())
                    ->get();

                $relatedProducts = $relatedProducts->merge($additionalProducts);
            }

            return RelatedProductResource::collection($relatedProducts);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to fetch related products',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}