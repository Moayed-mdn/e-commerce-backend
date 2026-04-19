<?php
namespace App\Exceptions;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ExceptionRegistrar
{
    public function handle(Exceptions $exceptions): void
    {
        $exceptions->render(function (Throwable $e) {
            if ($e instanceof BaseApiException) {
                return $e->render(request());
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => false,
                    'message' => __('error.validation_failed'),
                    'error_code' => ErrorCode::VAL_001->value,
                    'errors' => $e->errors(),
                ], 422);
            }

            if ($e instanceof HttpExceptionInterface) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                    'error_code' => "HTTP_{$e->getStatusCode()}",
                    'errors' => null,
                ], $e->getStatusCode());
            }

            Log::error($e);

            return response()->json([
                'status' => false,
                'message' => config('app.env') === 'local' ? $e->getMessage() : __('error.internal_server_error'),
                'error_code' => ErrorCode::SYS_001->value,
                'errors' => null,
            ], 500);
        });
    }
}