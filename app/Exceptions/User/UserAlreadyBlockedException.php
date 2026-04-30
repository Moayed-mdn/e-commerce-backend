<?php

namespace App\Exceptions\User;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class UserAlreadyBlockedException extends BaseApiException
{
    public function __construct()
    {
        parent::__construct(
            message: __('error.user_already_blocked'),
            statusCode: 400,
            errorCode: ErrorCode::USR_002->value,
        );
    }
}
