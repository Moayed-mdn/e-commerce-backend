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
            categoryId: self::nullableInteger($request, 'category_id'),
            brandId: self::nullableInteger($request, 'brand_id'),
            isActive: self::optionalBoolean($request, 'is_active'),
            translations: $request->input('translations'),
            variants: $request->input('variants'),
            tags: $request->input('tags'),
        );
    }

    private static function nullableInteger(UpdateProductRequest $request, string $key): ?int
    {
        if (!$request->exists($key)) {
            return null;
        }

        $value = $request->input($key);

        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private static function optionalBoolean(UpdateProductRequest $request, string $key): ?bool
    {
        if (!$request->exists($key)) {
            return null;
        }

        return $request->boolean($key);
    }
}
