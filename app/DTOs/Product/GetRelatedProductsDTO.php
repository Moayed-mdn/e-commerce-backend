<?php

declare(strict_types=1);

namespace App\DTOs\Product;

use App\Http\Requests\Product\GetRelatedProductsRequest;

class GetRelatedProductsDTO
{
    public function __construct(
        public string $slug,
    ) {}

    public static function fromRequest(GetRelatedProductsRequest $request): self
    {
        return new self((string) $request->route('slug'));
    }
}
