<?php

declare(strict_types=1);

namespace App\DTOs\Product;

use App\Http\Requests\HomePage\GetBestSellersRequest;

class GetBestSellersDTO
{
    public function __construct(
        public int $storeId,
        public int $limit,
    ) {}

    public static function fromRequest(GetBestSellersRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            limit: $request->integer('limit', 20),
        );
    }
}
