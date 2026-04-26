<?php

namespace App\DTOs\Product;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ProductCardDTO
{
    public function __construct(
        public int $product_id,
        public int $product_variant_id,
        public string $slug,
        public int $category_id,
        public string $primary_image,
        public ?string $alt_text,
        public string $product_name,
        public string $price,
        public string $description,
        public ?int $total_sold,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            product_id: $data['product_id'],
            product_variant_id: $data['product_variant_id'],
            slug: $data['slug'],
            category_id: $data['category_id'],
            primary_image: $data['primary_image'],
            alt_text: $data['alt_text'] ?? null,
            product_name: $data['product_name'],
            price: $data['price'],
            description: $data['description'],
            total_sold: $data['total_sold'] ?? null,
        );
    }
}