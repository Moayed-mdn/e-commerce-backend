<?php

namespace App\Exceptions\Auth;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class SocialAuthException extends BaseApiException
{
    public function __construct(
        ?string $message = null,
        int $statusCode = 401,
        string $errorCode = ErrorCode::AUTH_006->value,
        ?array $errors = null
    ) {
        parent::__construct($message ?? __('auth.social_auth_failed'), $statusCode, $errorCode, $errors);
    }
}
