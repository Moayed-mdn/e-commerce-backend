<?php

namespace App\Exceptions\Order;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class OrderNotFoundException extends BaseApiException
{
    public function __construct(?string $message = null, int $statusCode = 404, string $errorCode = ErrorCode::ORD_001->value, ?array $errors = null)
    {
        parent::__construct($message ?? __('error.order_not_found'), $statusCode, $errorCode, $errors);
    }
}
