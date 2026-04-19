<?php
// app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\FilterProductsRequest;
use App\Http\Resources\RelatedProductResource;
use App\Http\Resources\ProductCardResource;
use App\Http\Resources\ProductDetailResource;
use App\Services\ProductService;
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
     */
    public function index(FilterProductsRequest $request): JsonResponse
    {
        $query = $this->productService->buildBaseProductQuery();

        $descendants = $this->productService->getCategoryDescendants();

        if ($request->filled('category_slug')) {
            $category = $this->productService->findCategoryBySlugOrFail($request->category_slug);

            $descendantsWithSelf = $category->allDescendantIds()->push($category->id);

            $query->whereHas('category', function ($query) use ($descendantsWithSelf) {
                $query->whereIn('id', $descendantsWithSelf);
            });
        }

        $variantStatus = $this->productService->getProductFilterRanges($query);

        $query = $this->productService->applyFilters($query, $request);

        $perPage = $request->get('per_page', 20);
        $paginator = $query->paginate($perPage);

        return $this->paginated(
            $paginator,
            ProductCardResource::collection($paginator->items()),
            [
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
    public function indexByCategory(string $slug, Request $request): JsonResponse
    {
        $locale = app()->getLocale();

        $category = $this->productService->findCategoryBySlugOrFail($slug);
        $category->loadMissing(['translations', 'descendants.translations']);

        $descendantCategories = $this->productService->flattenCategoryDescendants($category, $locale);

        $query = $this->productService->buildBaseProductQuery()
            ->addSelect('images.alt_text as alt_text');

        if ($request->filled('category_slug')) {
            $subCategory = $this->productService->findCategoryBySlug($request->category_slug);

            if ($subCategory) {
                $subIds = $subCategory->allDescendantIds();
                $query->whereIn('products.category_id', $subIds);
            }
        } else {
            $allIds = $category->allDescendantIds();
            $query->whereIn('products.category_id', $allIds);
        }

        $variantStatus = $this->productService->getProductFilterRanges($query);

        $query = $this->productService->applyFilters($query, $request);

        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        $categoryTranslation = $category->translation($locale);

        return $this->paginated(
            $products,
            ProductCardResource::collection($products->items()),
            [
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

        return $this->success(new ProductDetailResource($product));
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

        return $this->success(RelatedProductResource::collection($relatedProducts));
    }
}