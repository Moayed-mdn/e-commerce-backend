<?php

namespace App\Exceptions\Payment;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class StripeWebhookException extends BaseApiException
{
    public function __construct(
        string $message = 'Stripe webhook error.',
        int $statusCode = 400,
        string $errorCode = ErrorCode::PMT_003->value,
        ?array $errors = null
    ) {
        parent::__construct($message, $statusCode, $errorCode, $errors);
    }
}
