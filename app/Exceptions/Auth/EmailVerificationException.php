<?php

namespace App\Exceptions\Auth;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class EmailVerificationException extends BaseApiException
{
    public function __construct(
        ?string $message = null,
        int $statusCode = 403,
        string $errorCode = ErrorCode::AUTH_007->value,
        ?array $errors = null
    ) {
        parent::__construct($message ?? __('auth.email_verification_failed'), $statusCode, $errorCode, $errors);
    }
}
