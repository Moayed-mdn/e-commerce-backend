<?php

namespace App\Exceptions\Auth;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class TooManyRequestsException extends BaseApiException
{
    public function __construct(
        string $message = 'Too many requests.',
        int $statusCode = 429,
        string $errorCode = ErrorCode::AUTH_008->value,
        ?array $errors = null
    ) {
        parent::__construct($message, $statusCode, $errorCode, $errors);
    }
}
