<?php

namespace App\DTOs;

class UpdateCartItemDTO
{
    public function __construct(
        public int $itemId,
        public int $quantity,
        public int $userId,
    ) {}

    public static function fromRequest(\App\Http\Requests\Cart\UpdateItemRequest $request, int $itemId): self
    {
        return new self(
            $itemId,
            $request->integer('quantity'),
            $request->user()->id,
        );
    }
}
