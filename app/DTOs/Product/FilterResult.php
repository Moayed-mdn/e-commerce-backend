<?php

declare(strict_types=1);

namespace App\DTOs\Product;

class FilterResult
{
    /**
     * @param array<int, array{id: int, slug: string, name: string}> $descendants
     */
    public function __construct(
        public readonly object $paginator,
        public readonly array $descendants,
        public readonly ?float $minPrice,
        public readonly ?float $maxPrice,
        public readonly ?string $earliestManufacture,
        public readonly ?string $latestExpiry,
    ) {}
}
