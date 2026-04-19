<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, string $message = 'success', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => __($message),
            'data' => $data,
        ], $statusCode);
    }

    public static function paginated($paginator, $data, $additionalMeta = [], string $message = 'success', $code = 200)
    {
        return response()->json([
            'status'  => true,
            'message' => __($message),
            'data'    => $data,
            'meta' => [
                'pagination' => [
                    'total' => $paginator->total(),
                    'count' => count($data),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'total_pages' => $paginator->lastPage(),
                ],
                ...$additionalMeta
            ],
        ], $code);
    }
}