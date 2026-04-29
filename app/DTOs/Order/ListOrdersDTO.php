<?php

declare(strict_types=1);

namespace App\DTOs\Order;

use Illuminate\Http\Request;

class ListOrdersDTO
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
