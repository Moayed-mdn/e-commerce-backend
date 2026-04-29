<?php

namespace App\Exceptions\Store;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class UnauthorizedStoreAccessException extends BaseApiException
{
    public function __construct()
    {
        parent::__construct(
            message: __('error.unauthorized_store'),
            statusCode: 403,
            errorCode: ErrorCode::STR_002->value,
        );
    }
}
