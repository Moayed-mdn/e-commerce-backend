<?php

namespace App\Exceptions\Order;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class OrderCancellationException extends BaseApiException
{
    public function __construct(string $message = "This order cannot be cancelled.", int $statusCode = 422, string $errorCode = ErrorCode::ORDER_002->value, ?array $errors = null)
    {
        parent::__construct($message, $statusCode, $errorCode, $errors);
    }
}