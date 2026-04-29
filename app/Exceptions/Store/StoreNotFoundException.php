<?php

namespace App\Exceptions\Store;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class StoreNotFoundException extends BaseApiException
{
    public function __construct()
    {
        parent::__construct(
            message: __('error.store_not_found'),
            statusCode: 404,
            errorCode: ErrorCode::STR_001->value,
        );
    }
}
