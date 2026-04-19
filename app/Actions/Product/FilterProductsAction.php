<?php

declare(strict_types=1);

namespace App\Actions\Product;

use App\Repositories\Category\CategoryRepository;
use App\Repositories\Product\ProductRepository;
use App\DTOs\Product\FilterProductsDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FilterProductsAction
{
    public function __construct(
        private ProductRepository $productRepository,
        private CategoryRepository $categoryRepository,
    ) {}

    public function execute(FilterProductsDTO $dto): array
    {
        $query = $this->productRepository->buildBaseQuery();

        // Get filter ranges before applying filters
        $filterRanges = $this->productRepository->getFilterRanges($query);

        // Get root categories for descendants
        $descendants = $this->categoryRepository->getRootCategories();

        // Apply category filter if provided
        if ($dto->categorySlug !== null) {
            $category = $this->categoryRepository->findBySlugOrFail($dto->categorySlug);
            $descendantsWithSelf = $category->allDescendantIds()->push($category->id);
            $query = $this->productRepository->filterByCategory($query, $descendantsWithSelf->toArray());
        }

        // Apply filters
        $query = $this->productRepository->applyPriceFilter($query, $dto->minPrice, $dto->maxPrice);
        $query = $this->productRepository->applyManufactureFilter($query, $dto->earliestManufacture);
        $query = $this->productRepository->applyExpiryFilter($query, $dto->latestExpiry);

        // Paginate
        $paginator = $this->productRepository->paginate($query, $dto->perPage);

        return [
            'paginator' => $paginator,
            'filters' => [
                'descendants' => $descendants->map(fn($cat) => [
                    'id' => $cat->id,
                    'slug' => $cat->slug,
                    'name' => $cat->name,
                ])->toArray(),
                'min_price' => $filterRanges->min_price ?? null,
                'max_price' => $filterRanges->max_price ?? null,
                'earliest_manufacture' => $filterRanges->earliest_manufacture ?? null,
                'latest_expiry' => $filterRanges->latest_expiry ?? null,
            ],
        ];
    }
}
