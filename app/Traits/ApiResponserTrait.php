<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ApiResponserTrait
{

    protected function SuccessResponse(?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => 'Success',
            'message' => $message,
        ], $code);
    }
    
    protected function dataSuccessResponse($data, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => 'Success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

 

}
