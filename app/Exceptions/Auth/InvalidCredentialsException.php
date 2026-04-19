<?php

namespace App\Exceptions\Auth;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class InvalidCredentialsException extends BaseApiException
{
    public function __construct(?string $message = null, int $statusCode = 401, string $errorCode = ErrorCode::AUTH_001->value, ?array $errors = null)
    {
        parent::__construct($message ?? __('auth.invalid_credentials'), $statusCode, $errorCode, $errors);
    }
}