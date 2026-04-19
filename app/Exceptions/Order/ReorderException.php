<?php

namespace App\Exceptions\Order;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class ReorderException extends BaseApiException
{
    public function __construct(
        ?string $message = null,
        int $statusCode = 422,
        string $errorCode = ErrorCode::ORD_003->value,
        ?array $errors = null
    ) {
        parent::__construct($message ?? __('services.reorder_items_not_added'), $statusCode, $errorCode, $errors);
    }
}
