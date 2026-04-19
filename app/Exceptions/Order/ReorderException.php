<?php

namespace App\Exceptions\Order;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class ReorderException extends BaseApiException
{
    public function __construct(
        string $message = 'None of the items could be added to your cart.',
        int $statusCode = 422,
        string $errorCode = ErrorCode::ORD_003->value,
        ?array $errors = null
    ) {
        parent::__construct($message, $statusCode, $errorCode, $errors);
    }
}
