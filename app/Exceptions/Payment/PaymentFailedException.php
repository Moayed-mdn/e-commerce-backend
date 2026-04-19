<?php

namespace App\Exceptions\Payment;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class PaymentFailedException extends BaseApiException
{
    public function __construct(?string $message = null, int $statusCode = 400, string $errorCode = ErrorCode::PMT_001->value, ?array $errors = null)
    {
        parent::__construct($message ?? __('services.payment_failed'), $statusCode, $errorCode, $errors);
    }
}