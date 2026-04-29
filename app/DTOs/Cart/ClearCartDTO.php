<?php

namespace App\DTOs\Cart;

class ClearCartDTO
{
    public function __construct(
        public int $storeId,
        public int $userId,
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            userId: $request->user()->id,
        );
    }
}
