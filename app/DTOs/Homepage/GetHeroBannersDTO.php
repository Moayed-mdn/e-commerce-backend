<?php

declare(strict_types=1);

namespace App\DTOs\Homepage;

use App\Http\Requests\HomePage\GetHeroBannersRequest;

class GetHeroBannersDTO
{
    public function __construct(
        public int $storeId,
    ) {}

    public static function fromRequest(GetHeroBannersRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
        );
    }
}
