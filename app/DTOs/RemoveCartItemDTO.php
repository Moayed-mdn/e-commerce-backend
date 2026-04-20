<?php

namespace App\DTOs;

class RemoveCartItemDTO
{
    public function __construct(
        public int $itemId,
        public int $userId,
    ) {}

    public static function fromRequest(\App\Http\Requests\Cart\RemoveItemRequest $request): self
    {
        return new self(
            (int) $request->route('itemId'),
            $request->user()->id,
        );
    }
}
