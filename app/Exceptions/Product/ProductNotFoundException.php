<?php

declare(strict_types=1);

namespace App\Exceptions\Product;

use App\Exceptions\BaseApiException;
use App\Enums\ErrorCode;

class ProductNotFoundException extends BaseApiException
{
    public function __construct()
    {
        parent::__construct(
            message: __('error.product_not_found'),
            errorCode: ErrorCode::PRD_001->value,
            statusCode: 404
        );
    }
}
