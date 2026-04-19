<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Search\SearchRequest;
use App\Services\Search\SearchService;
use App\DTOs\Search\SearchDTO;
use App\Traits\ApiResponserTrait;

class SearchController extends Controller
{
    use ApiResponserTrait;

    public function __construct(
        private SearchService $searchService,
    ) {}

    public function index(SearchRequest $request)
    {
        $dto = SearchDTO::fromRequest($request);
        $results = $this->searchService->execute($dto);

        if ($results['type'] === 'all') {
            return $this->success([
                'type' => 'all',
                'products' => $results['results']['products'],
                'categories' => $results['results']['categories'],
            ], 'Search results retrieved successfully');
        }

        return $this->paginated(
            paginator: $results['results'],
            message: 'Search results retrieved successfully',
            additionalMeta: ['type' => $results['type']]
        );
    }
}