<?php

namespace App\DTOs\Cart;

class UpdateCartItemDTO
{
    public function __construct(
        public int $itemId,
        public int $quantity,
        public int $userId,
    ) {}

    public static function fromRequest(\App\Http\Requests\Cart\UpdateItemRequest $request): self
    {
        return new self(
            (int) $request->route('itemId'),
            $request->integer('quantity'),
            $request->user()->id,
        );
    }
}
