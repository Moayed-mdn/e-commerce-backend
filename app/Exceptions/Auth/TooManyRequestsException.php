<?php

namespace App\Exceptions\Auth;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class TooManyRequestsException extends BaseApiException
{
    public function __construct(
        ?string $message = null,
        int $statusCode = 429,
        string $errorCode = ErrorCode::AUTH_008->value,
        ?array $errors = null
    ) {
        parent::__construct($message ?? __('auth.too_many_requests'), $statusCode, $errorCode, $errors);
    }
}
