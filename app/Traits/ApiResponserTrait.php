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

    public function paginated($resourceCollection, ?string $message = null, int $statusCode = 200, ?array $additionalMeta = null): JsonResponse
    {
        $response = [
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
        ];

        if ($additionalMeta !== null) {
            $response['meta'] = array_merge($response['meta'], $additionalMeta);
        }

        return response()->json($response, $statusCode);
    }
}