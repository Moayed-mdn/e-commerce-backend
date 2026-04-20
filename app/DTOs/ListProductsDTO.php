<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Product\FilterProductsRequest;

class ListProductsDTO
{
    public function __construct(
        public ?string $categorySlug = null,
        public ?float $minPrice = null,
        public ?float $maxPrice = null,
        public ?string $earliestManufacture = null,
        public ?string $latestExpiry = null,
        public int $perPage = 20,
    ) {}

    public static function fromRequest(FilterProductsRequest $request): self
    {
        return new self(
            $request->string('category_slug')->toString() ?: null,
            $request->filled('min_price') ? (float) $request->input('min_price') : null,
            $request->filled('max_price') ? (float) $request->input('max_price') : null,
            $request->string('earliest_manufacture')->toString() ?: null,
            $request->string('latest_expiry')->toString() ?: null,
            $request->integer('per_page', 20),
        );
    }
}
