<?php

declare(strict_types=1);

namespace App\DTOs\Search;

use App\Http\Requests\Search\SearchRequest;

readonly class SearchDTO
{
    public function __construct(
        public int $storeId,
        public string $query,
        public ?string $categorySlug = null,
        public string $type = 'all',
        public int $limit = 15,
        public int $page = 1,
    ) {}

    public static function fromRequest(SearchRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            query: $request->validated('query'),
            categorySlug: $request->validated('category_slug'),
            type: $request->validated('type', 'all'),
            limit: (int) $request->validated('limit', 15),
            page: (int) $request->validated('page', 1),
        );
    }
}
