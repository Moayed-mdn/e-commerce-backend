<?php

namespace App\DTOs;

class RemoveCartItemDTO
{
    public function __construct(
        public int $itemId,
        public int $userId,
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request, int $itemId): self
    {
        return new self(
            $itemId,
            $request->user()->id,
        );
    }
}
