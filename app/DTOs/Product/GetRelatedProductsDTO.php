<?php

declare(strict_types=1);

namespace App\DTOs\Product;

use App\Http\Requests\Product\GetRelatedProductsRequest;

class GetRelatedProductsDTO
{
    public function __construct(
        public int $storeId,
        public string $slug,
    ) {}

    public static function fromRequest(GetRelatedProductsRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            slug: (string) $request->route('slug'),
        );
    }
}
