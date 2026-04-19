<?php

namespace App\Exceptions\Order;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class OutOfStockException extends BaseApiException
{
    public function __construct(string $message = "Item is out of stock.", int $statusCode = 400, string $errorCode = ErrorCode::ORDER_001->value, ?array $errors = null)
    {
        parent::__construct($message, $statusCode, $errorCode, $errors);
    }
}