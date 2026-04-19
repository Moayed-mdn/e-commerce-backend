<?php

namespace App\Exceptions\Auth;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class UnauthorizedException extends BaseApiException
{
    public function __construct(string $message = "Unauthorized.", int $statusCode = 403, string $errorCode = ErrorCode::AUTH_002->value, ?array $errors = null)
    {
        parent::__construct($message, $statusCode, $errorCode, $errors);
    }
}