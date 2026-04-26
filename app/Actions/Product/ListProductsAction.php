<?php

declare(strict_types=1);

namespace App\Actions\Product;

use App\DTOs\Product\ListProductsDTO;
use App\Services\ProductService;
use Illuminate\Pagination\LengthAwarePaginator;

class ListProductsAction
{
    public function __construct(
        private ProductService $productService,
    ) {}

    public function execute(ListProductsDTO $dto): array
    {
        $query = $this->productService->buildBaseProductQuery();

        $descendants = $this->productService->getCategoryDescendants();

        if ($dto->categorySlug) {
            $category = $this->productService->findCategoryBySlugOrFail($dto->categorySlug);
            $descendantsWithSelf = $category->allDescendantIds();

            $query->whereHas('category', function ($query) use ($descendantsWithSelf) {
                $query->whereIn('id', $descendantsWithSelf);
            });
        }

        $variantStatus = $this->productService->getProductFilterRanges($query);

        if ($dto->minPrice !== null) {
            $query->whereHas('variants', function ($q) use ($dto) {
                $q->where('price', '>=', $dto->minPrice);
            });
        }

        if ($dto->maxPrice !== null) {
            $query->whereHas('variants', function ($q) use ($dto) {
                $q->where('price', '<=', $dto->maxPrice);
            });
        }

        if ($dto->earliestManufacture) {
            $query->whereHas('variants', function ($q) use ($dto) {
                $q->where('manufacture_date', '>=', $dto->earliestManufacture);
            });
        }

        if ($dto->latestExpiry) {
            $query->whereHas('variants', function ($q) use ($dto) {
                $q->where('expiry_date', '>=', $dto->latestExpiry);
            });
        }

        $paginator = $query->paginate($dto->perPage);

        return [
            'paginator' => $paginator,
            'descendants' => $descendants,
            'variant_status' => $variantStatus,
        ];
    }
}