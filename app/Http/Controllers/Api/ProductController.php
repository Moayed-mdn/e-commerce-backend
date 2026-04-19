<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\FilterProductsRequest;
use App\Http\Requests\Product\FilterProductsByCategoryRequest;
use App\Http\Resources\RelatedProductResource;
use App\Http\Resources\ProductCardResource;
use App\Http\Resources\ProductDetailResource;
use App\Actions\Product\FilterProductsAction;
use App\Actions\Product\FilterProductsByCategoryAction;
use App\Actions\Product\GetProductDetailAction;
use App\Actions\Product\GetRelatedProductsAction;
use App\DTOs\Product\FilterProductsDTO;
use App\DTOs\Product\FilterProductsByCategoryDTO;
use App\DTOs\Product\GetProductDetailDTO;
use App\DTOs\Product\GetRelatedProductsDTO;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        protected FilterProductsAction $filterProductsAction,
        protected FilterProductsByCategoryAction $filterProductsByCategoryAction,
        protected GetProductDetailAction $getProductDetailAction,
        protected GetRelatedProductsAction $getRelatedProductsAction,
    ) {
    }

    /**
     * List products (paginated, filterable).
     */
    public function index(FilterProductsRequest $request): JsonResponse
    {
        $dto = FilterProductsDTO::fromRequest($request);

        $result = $this->filterProductsAction->execute($dto);

        return $this->paginated(
            ProductCardResource::collection($result->paginator),
            null,
            200,
            [
                'descendants' => $result->descendants,
                'min_price' => $result->minPrice,
                'max_price' => $result->maxPrice,
                'earliest_manufacture' => $result->earliestManufacture,
                'latest_expiry' => $result->latestExpiry,
            ]
        );
    }

    /**
     * Products filtered by category (localized slug).
     *
     * GET /products/category/{slug}
     * GET /products/category/{slug}?category_slug=sub-category&min_price=10&max_price=100
     */
    public function indexByCategory(string $slug, FilterProductsByCategoryRequest $request): JsonResponse
    {
        $dto = FilterProductsByCategoryDTO::fromRequest($slug, $request);

        $result = $this->filterProductsByCategoryAction->execute($dto);

        return $this->paginated(
            ProductCardResource::collection($result->paginator),
            null,
            200,
            [
                'category' => [
                    'id' => $result->categoryId,
                    'name' => $result->categoryName,
                    'slug' => $result->categorySlug,
                    'breadcrumb' => $result->breadcrumb,
                ],
                'descendants' => $result->descendants,
                'min_price' => $result->minPrice,
                'max_price' => $result->maxPrice,
                'earliest_manufacture' => $result->earliestManufacture,
                'latest_expiry' => $result->latestExpiry,
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
        $dto = GetProductDetailDTO::fromRequest($slug);

        $product = $this->getProductDetailAction->execute($dto);

        return $this->success(new ProductDetailResource($product));
    }

    /**
     * Get related products by localized slug.
     *
     * GET /products/{slug}/related
     */
    public function related(string $slug): JsonResponse
    {
        $dto = GetRelatedProductsDTO::fromRequest($slug);

        $relatedProducts = $this->getRelatedProductsAction->execute($dto);

        return $this->success(RelatedProductResource::collection($relatedProducts));
    }
}