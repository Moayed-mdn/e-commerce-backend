<?php

namespace App\DTOs\Cart;

class RemoveCartItemDTO
{
    public function __construct(
        public int $storeId,
        public int $itemId,
        public int $userId,
    ) {}

    public static function fromRequest(\App\Http\Requests\Cart\RemoveItemRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            itemId: (int) $request->route('itemId'),
            userId: $request->user()->id,
        );
    }
}
