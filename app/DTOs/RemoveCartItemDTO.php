<?php

namespace App\DTOs;

class RemoveCartItemDTO
{
    public function __construct(
        public int $itemId,
        public int $userId,
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            $request->route('item_id'),
            $request->user()->id,
        );
    }
}
