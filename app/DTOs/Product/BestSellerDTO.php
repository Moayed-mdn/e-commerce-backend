<?php

namespace App\DTOs\Product;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class BestSellerDTO
{
    public function __construct(
        public int $category_id,
        public string $category_name,
        public string $category_slug,
        /** @var ProductCardDTO[] */
        public array $products, // array of ProductCardDTO
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            category_id: $data['category_id'],
            category_name: $data['category_name'],
            category_slug: $data['category_slug'],
            products: array_map(
                fn ($product) => ProductCardDTO::fromArray($product),
                $data['products']
            )
        );
    }
}