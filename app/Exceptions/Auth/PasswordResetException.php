<?php

namespace App\Exceptions\Auth;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class PasswordResetException extends BaseApiException
{
    public function __construct(
        string $message = 'Password reset failed.',
        int $statusCode = 400,
        string $errorCode = ErrorCode::AUTH_005->value,
        ?array $errors = null
    ) {
        parent::__construct($message, $statusCode, $errorCode, $errors);
    }
}
