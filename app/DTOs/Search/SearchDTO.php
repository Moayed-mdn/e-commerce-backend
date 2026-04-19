<?php

declare(strict_types=1);

namespace App\DTOs\Search;

use App\Http\Requests\Search\SearchRequest;

readonly class SearchDTO
{
    public function __construct(
        public string $query,
        public string $type = 'all',
        public int $limit = 15,
        public int $page = 1,
    ) {}

    public static function fromRequest(SearchRequest $request): self
    {
        return new self(
            query: $request->validated('query'),
            type: $request->validated('type', 'all'),
            limit: (int) $request->validated('limit', 15),
            page: (int) $request->validated('page', 1),
        );
    }
}
