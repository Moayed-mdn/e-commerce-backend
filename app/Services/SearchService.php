<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Search\SearchRepository;
use App\DTOs\Search\SearchDTO;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    public function __construct(
        private SearchRepository $repository,
    ) {}

    public function execute(SearchDTO $dto): array
    {
        return match ($dto->type) {
            'products' => [
                'type' => 'products',
                'results' => $this->repository->searchProducts($dto->query, $dto->storeId, $dto->limit, $dto->page),
            ],
            'categories' => [
                'type' => 'categories',
                'results' => $this->repository->searchCategories($dto->query, $dto->storeId, $dto->limit, $dto->page),
            ],
            default => [
                'type' => 'all',
                'results' => $this->repository->searchAll($dto->query, $dto->storeId, $dto->limit, $dto->page),
            ],
        };
    }
}
