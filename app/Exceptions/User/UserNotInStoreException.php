<?php

namespace App\Exceptions\User;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class UserNotInStoreException extends BaseApiException
{
    public function __construct()
    {
        parent::__construct(
            message: __('error.user_not_in_store'),
            statusCode: 403,
            errorCode: ErrorCode::USR_004->value,
        );
    }
}
