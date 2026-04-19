<?php

namespace App\Exceptions\Payment;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class StripeServiceException extends BaseApiException
{
    public function __construct(
        string $message = 'A Stripe service error occurred.',
        int $statusCode = 500,
        string $errorCode = ErrorCode::PMT_004->value,
        ?array $errors = null
    ) {
        parent::__construct($message, $statusCode, $errorCode, $errors);
    }
}
