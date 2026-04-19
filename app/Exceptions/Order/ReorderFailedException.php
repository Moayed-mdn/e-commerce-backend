<?php

namespace App\Exceptions\Order;

use App\Enums\ErrorCode;
use App\Exceptions\BaseApiException;

class ReorderFailedException extends BaseApiException
{
    protected int $statusCode = 422;

    protected string $errorCode = ErrorCode::ORD_003->value;

    public function __construct(string $message = 'Failed to reorder the items.')
    {
        parent::__construct($message);
    }
}
