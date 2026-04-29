<?php

namespace App\DTOs\Cart;

use Illuminate\Http\Request;

class GetCartDTO
{
    public function __construct(
        public int $storeId,
        public int $userId,
    ) {}

    public static function fromRequest(Request $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            userId: $request->user()->id,
        );
    }
}
