<?php

declare(strict_types=1);

namespace App\DTOs\Product;

class GetRelatedProductsDTO
{
    public function __construct(
        public string $slug,
        public int $limit,
    ) {}

    public static function fromRequest(string $slug, int $limit = 8): self
    {
        return new self(slug: $slug, limit: $limit);
    }
}
