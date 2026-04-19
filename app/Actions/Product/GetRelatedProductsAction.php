<?php

declare(strict_types=1);

namespace App\Actions\Product;

use App\Models\Product;
use App\Repositories\Product\ProductRepository;
use App\DTOs\Product\GetRelatedProductsDTO;
use Illuminate\Database\Eloquent\Collection;

class GetRelatedProductsAction
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {}

    public function execute(GetRelatedProductsDTO $dto): Collection
    {
        $currentProduct = $this->productRepository->findBySlug($dto->slug);

        if (!$currentProduct) {
            throw new \App\Exceptions\Product\ProductNotFoundException();
        }

        return $this->productRepository->findRelatedProducts($currentProduct, $dto->limit);
    }
}
