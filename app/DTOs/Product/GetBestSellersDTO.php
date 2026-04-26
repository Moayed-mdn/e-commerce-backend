<?php

declare(strict_types=1);

namespace App\DTOs\Product;

use App\Http\Requests\HomePage\GetBestSellersRequest;

class GetBestSellersDTO
{
    public function __construct(
        public int $limit,
    ) {}

    public static function fromRequest(GetBestSellersRequest $request): self
    {
        return new self(
            $request->integer('limit', 20),
        );
    }
}
