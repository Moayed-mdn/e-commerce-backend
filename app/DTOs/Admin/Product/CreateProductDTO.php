<?php

namespace App\DTOs\Admin\Product;

use App\Http\Requests\Admin\Product\CreateProductRequest;

class CreateProductDTO
{
    public function __construct(
        public int $storeId,
        public ?int $categoryId,
        public ?int $brandId,
        public bool $isActive,
        public array $translations,
        public array $variants,
        public array $tags = [],
    ) {}

    public static function fromRequest(CreateProductRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            categoryId: $request->integer('category_id'),
            brandId: $request->integer('brand_id'),
            isActive: $request->boolean('is_active', true),
            translations: $request->input('translations', []),
            variants: $request->input('variants', []),
            tags: $request->input('tags', []),
        );
    }
}
