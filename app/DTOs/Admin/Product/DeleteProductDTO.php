<?php

namespace App\DTOs\Admin\Product;

use App\Http\Requests\Admin\Product\DeleteProductRequest;

class DeleteProductDTO
{
    public function __construct(
        public int $storeId,
        public int $productId,
    ) {}

    public static function fromRequest(DeleteProductRequest $request, int $storeId, int $productId): self
    {
        return new self(
            storeId: $storeId,
            productId: $productId,
        );
    }
}
