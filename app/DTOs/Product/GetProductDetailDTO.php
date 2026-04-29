<?php

declare(strict_types=1);

namespace App\DTOs\Product;

use App\Http\Requests\Product\GetProductDetailRequest;

class GetProductDetailDTO
{
    public function __construct(
        public int $storeId,
        public string $slug,
    ) {}

    public static function fromRequest(GetProductDetailRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            slug: (string) $request->route('slug'),
        );
    }
}
