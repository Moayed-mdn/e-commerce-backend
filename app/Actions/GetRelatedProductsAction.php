<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\GetRelatedProductsDTO;
use App\Services\ProductService;
use Illuminate\Support\Collection;

class GetRelatedProductsAction
{
    public function __construct(
        private ProductService $productService,
    ) {}

    public function execute(GetRelatedProductsDTO $dto): Collection
    {
        $currentProduct = $this->productService->findProductBySlugOrFail($dto->slug);

        return $this->productService->getRelatedProducts($currentProduct);
    }
}
