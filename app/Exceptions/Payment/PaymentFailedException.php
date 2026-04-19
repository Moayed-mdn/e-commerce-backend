<?php

namespace App\Exceptions\Payment;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class PaymentFailedException extends BaseApiException
{
    public function __construct(string $message = "Payment failed.", int $statusCode = 400, string $errorCode = ErrorCode::PAY_001->value, ?array $errors = null)
    {
        parent::__construct($message, $statusCode, $errorCode, $errors);
    }
}