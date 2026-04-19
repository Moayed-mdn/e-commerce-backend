<?php
// app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Models\Category;
use App\Http\Resources\RelatedProductResource;
use App\Http\Resources\ProductCardResource;
use App\Http\Resources\ProductDetailResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {
    }
    /**
     * List products (paginated, filterable).
     * UNCHANGED — keeping your existing index() method exactly as-is.
     */
    public function index(Request $request)
    {
        $query = $this->productService->buildBaseProductQuery();

        $descendants = $this->productService->getCategoryDescendants();

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

        $variantStatus = $this->productService->getProductFilterRanges($query);

        $query = $this->productService->applyFilters($query, $request);

        $perPage = $request->get('per_page', 20);
        $paginator = $query->paginate($perPage);

        return ApiResponse::paginated(
            paginator: $paginator,
            data: ProductCardResource::collection($paginator->items()),
            additionalMeta: [
                'filters' => [
                    'descendants' => $descendants,
                    'min_price' => $variantStatus->min_price,
                    'max_price' => $variantStatus->max_price,
                    'earliest_manufacture' => $variantStatus->earliest_manufacture,
                    'latest_expiry' => $variantStatus->latest_expiry
                ]
            ]
        );        
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
        $category = $this->productService->findCategoryBySlugOrFail($slug);
        $category->loadMissing(['translations', 'descendants.translations']);

        // ── Build subcategory filter list (translated) ──
        $descendantCategories = $this->productService->flattenCategoryDescendants($category, $locale);

        // ── Main product query (same pattern as index()) ──
        $query = $this->productService->buildBaseProductQuery()
            ->addSelect('images.alt_text as alt_text');

        // ── Filter by sub-category slug (from query param) ──
        if ($request->filled('category_slug')) {
            $subCategory = $this->productService->findCategoryBySlug($request->category_slug);

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
        $variantStatus = $this->productService->getProductFilterRanges($query);

        // ── Apply price/date filters ──
        $query = $this->productService->applyFilters($query, $request);

        // ── Paginate ──
        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        // ── Category translation for the response ──
        $categoryTranslation = $category->translation($locale);

        return ApiResponse::paginated(
            paginator: $products,
            data: ProductCardResource::collection($products->items()),
            additionalMeta: [
                'category' => [
                    'id'         => $category->id,
                    'name'       => $categoryTranslation?->name ?? $category->slug,
                    'slug'       => $categoryTranslation?->slug ?? $category->slug,
                    'breadcrumb' => $category->breadcrumb,
                ],
                'filters' => [
                    'descendants'          => $descendantCategories,
                    'min_price'            => $variantStatus->min_price ?? null,
                    'max_price'            => $variantStatus->max_price ?? null,
                    'earliest_manufacture' => $variantStatus->earliest_manufacture ?? null,
                    'latest_expiry'        => $variantStatus->latest_expiry ?? null,
                ],
            ]
        );
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
    public function show(string $slug): JsonResponse
    {
        $product = $this->productService->findProductBySlugOrFail($slug);

        $product->load([
            'translations',
            'category.translations',
            'brand',
            'activeVariants.attributeValues.translations',
            'activeVariants.attributeValues.attribute.translations',
            'activeVariants.images',
        ]);

        return ApiResponse::success(new ProductDetailResource($product));
    }

    /**
     * Get related products by localized slug.
     *
     * GET /products/{slug}/related
     */
    public function related(string $slug): JsonResponse
    {
        $currentProduct = $this->productService->findProductBySlugOrFail($slug);

        $relatedProducts = $this->productService->getRelatedProducts($currentProduct);

        return ApiResponse::success(RelatedProductResource::collection($relatedProducts));
    }
}