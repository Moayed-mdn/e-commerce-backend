<?php

declare(strict_types=1);

namespace App\Actions\Product;

use App\Models\Product;
use App\Repositories\Product\ProductRepository;
use App\DTOs\Product\GetProductDetailDTO;

class GetProductDetailAction
{
    public function __construct(
        private ProductRepository $productRepository,
    ) {}

    public function execute(GetProductDetailDTO $dto): Product
    {
        $product = $this->productRepository->findBySlug($dto->slug);

        if (!$product) {
            throw new \App\Exceptions\Product\ProductNotFoundException();
        }

        $product->load([
            'translations',
            'category.translations',
            'brand',
            'activeVariants.attributeValues.translations',
            'activeVariants.attributeValues.attribute.translations',
            'activeVariants.images',
        ]);

        return $product;
    }
}
