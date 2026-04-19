<?php

declare(strict_types=1);

namespace App\DTOs\Product;

class GetProductDetailDTO
{
    public function __construct(
        public string $slug,
    ) {}

    public static function fromRequest(string $slug): self
    {
        return new self(slug: $slug);
    }
}
