<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @deprecated Use ApiResponserTrait instead via base Controller class.
 * 
 * According to ARCHITECTURE.md:
 * - Controllers MUST use ApiResponserTrait methods ($this->success(), $this->paginated())
 * - Returning response()->json() directly is FORBIDDEN
 * - This class is kept for backward compatibility only
 */
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

    public static function paginated(LengthAwarePaginator $paginator, $data, array $additionalMeta = [], string $message = 'success', int $code = 200): JsonResponse
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