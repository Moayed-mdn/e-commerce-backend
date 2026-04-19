<?php

namespace App\Exceptions;

use App\Enums\ErrorCode;

class NotFoundException extends BaseApiException
{
    public function __construct(?string $message = null, int $statusCode = 404, string $errorCode = ErrorCode::SYS_002->value, ?array $errors = null)
    {
        parent::__construct($message ?? __('error.not_found'), $statusCode, $errorCode, $errors);
    }
}