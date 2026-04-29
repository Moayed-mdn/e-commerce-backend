<?php

declare(strict_types=1);

namespace App\DTOs\Product;

use App\Http\Requests\Product\FilterProductsRequest;

class FilterProductsDTO
{
    public function __construct(
        public int $storeId,
        public ?string $categorySlug,
        public ?float $minPrice,
        public ?float $maxPrice,
        public ?string $earliestManufacture,
        public ?string $latestExpiry,
        public int $perPage,
    ) {}

    public static function fromRequest(FilterProductsRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            categorySlug: $request->filled('category_slug') ? $request->string('category_slug') : null,
            minPrice: $request->filled('min_price') ? (float) $request->get('min_price') : null,
            maxPrice: $request->filled('max_price') ? (float) $request->get('max_price') : null,
            earliestManufacture: $request->filled('earliest_manufacture') ? $request->string('earliest_manufacture') : null,
            latestExpiry: $request->filled('latest_expiry') ? $request->string('latest_expiry') : null,
            perPage: $request->get('per_page', 20),
        );
    }
}
