<?php

namespace App\Exceptions\User;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class UserNotFoundException extends BaseApiException
{
    public function __construct()
    {
        parent::__construct(
            message: __('error.user_not_found'),
            statusCode: 404,
            errorCode: ErrorCode::USR_001->value,
        );
    }
}
