<?php

declare(strict_types=1);

namespace App\DTOs\Product;

class CategoryFilterResult
{
    /**
     * @param array<int, array{id: int, slug: string, name: string}> $descendants
     * @param array<int, string> $breadcrumb
     */
    public function __construct(
        public readonly object $paginator,
        public readonly string $categoryId,
        public readonly string $categoryName,
        public readonly string $categorySlug,
        public readonly array $breadcrumb,
        public readonly array $descendants,
        public readonly ?float $minPrice,
        public readonly ?float $maxPrice,
        public readonly ?string $earliestManufacture,
        public readonly ?string $latestExpiry,
    ) {}
}
