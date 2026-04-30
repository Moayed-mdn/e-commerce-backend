<?php

namespace App\DTOs\Admin\Product;

use App\Http\Requests\Admin\Product\UpdateProductRequest;

class UpdateProductDTO
{
    public function __construct(
        public int $storeId,
        public int $productId,
        public ?int $categoryId = null,
        public ?int $brandId = null,
        public ?bool $isActive = null,
        public ?array $translations = null,
        public ?array $variants = null,
        public ?array $tags = null,
    ) {}

    public static function fromRequest(UpdateProductRequest $request, int $storeId, int $productId): self
    {
        return new self(
            storeId: $storeId,
            productId: $productId,
            categoryId: $request->integer('category_id'),
            brandId: $request->integer('brand_id'),
            isActive: $request->boolean('is_active'),
            translations: $request->input('translations'),
            variants: $request->input('variants'),
            tags: $request->input('tags'),
        );
    }
}
