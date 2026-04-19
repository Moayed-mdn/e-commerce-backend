<?php

namespace App\Exceptions\System;

use App\Exceptions\BaseApiException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Exception thrown when the request is well-formed but contains semantic errors.
 * Maps to HTTP 422 Unprocessable Content.
 */
class UnprocessableContentException extends BaseApiException
{
    public function __construct(
        string $message = 'Unprocessable Content',
        ?\Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct(
            message: $message,
            statusCode: 422,
            errorCode: 'UNPROCESSABLE_CONTENT',
            previous: $previous,
            context: $context
        );
    }
}