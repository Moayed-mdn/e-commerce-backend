<?php

namespace App\Exceptions;

use App\Enums\ErrorCode;

class NotFoundException extends BaseApiException
{
    public function __construct(string $message = "Not Found.", int $statusCode = 404, string $errorCode = ErrorCode::SYS_002->value, ?array $errors = null)
    {
        parent::__construct($message, $statusCode, $errorCode, $errors);
    }
}