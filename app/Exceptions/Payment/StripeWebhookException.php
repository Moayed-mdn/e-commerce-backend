<?php

namespace App\Exceptions\Payment;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class StripeWebhookException extends BaseApiException
{
    public function __construct(
        ?string $message = null,
        int $statusCode = 400,
        string $errorCode = ErrorCode::PMT_003->value,
        ?array $errors = null
    ) {
        parent::__construct($message ?? __('services.stripe_webhook_error'), $statusCode, $errorCode, $errors);
    }
}
