<?php

namespace App\DTOs\Cart;

class UpdateCartItemDTO
{
    public function __construct(
        public int $storeId,
        public int $itemId,
        public int $quantity,
        public int $userId,
    ) {}

    public static function fromRequest(\App\Http\Requests\Cart\UpdateItemRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            itemId: (int) $request->route('itemId'),
            quantity: $request->integer('quantity'),
            userId: $request->user()->id,
        );
    }
}
