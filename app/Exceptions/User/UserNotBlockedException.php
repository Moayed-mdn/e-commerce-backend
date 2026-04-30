<?php

namespace App\Exceptions\User;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class UserNotBlockedException extends BaseApiException
{
    public function __construct()
    {
        parent::__construct(
            message: __('error.user_not_blocked'),
            statusCode: 400,
            errorCode: ErrorCode::USR_003->value,
        );
    }
}
