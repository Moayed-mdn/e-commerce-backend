<?php

namespace App\DTOs\Cart;

class AddToCartDTO
{
    public function __construct(
        public int $storeId,
        public int $productVariantId,
        public int $quantity,
        public int $userId,
    ) {}

    public static function fromRequest(\App\Http\Requests\Cart\AddItemRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            productVariantId: $request->integer('product_variant_id'),
            quantity: $request->integer('quantity'),
            userId: $request->user()->id,
        );
    }
}
