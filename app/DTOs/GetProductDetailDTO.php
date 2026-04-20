<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Product\GetProductDetailRequest;

class GetProductDetailDTO
{
    public function __construct(
        public string $slug,
    ) {}

    public static function fromRequest(GetProductDetailRequest $request): self
    {
        return new self((string) $request->route('slug'));
    }
}
