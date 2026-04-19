<?php

namespace App\DTOs;

class AddToCartDTO
{
    public function __construct(
        public int $productVariantId,
        public int $quantity,
        public int $userId,
    ) {}

    public static function fromRequest(\App\Http\Requests\Cart\AddItemRequest $request): self
    {
        return new self(
            $request->integer('product_variant_id'),
            $request->integer('quantity'),
            $request->user()->id,
        );
    }
}
