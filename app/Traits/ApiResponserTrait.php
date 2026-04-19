<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiResponserTrait
{
    public function success($data = null, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message ? __($message) : null,
            'data'    => $data,
        ], $statusCode);
    }

    public function paginated($resourceCollection, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message ? __($message) : null,
            'data'    => $resourceCollection->collection,
            'meta'    => [
                'pagination' => [
                    'total'         => $resourceCollection->resource->total(),
                    'count'         => $resourceCollection->count(),
                    'per_page'      => $resourceCollection->resource->perPage(),
                    'current_page'  => $resourceCollection->resource->currentPage(),
                    'total_pages'   => $resourceCollection->resource->lastPage(),
                ],
            ],
        ], $statusCode);
    }
}