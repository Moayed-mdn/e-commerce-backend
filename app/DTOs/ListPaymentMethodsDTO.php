<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\PaymentMethod\ListPaymentMethodsRequest;

class ListPaymentMethodsDTO
{
    public function __construct(
        public int $userId,
    ) {}

    public static function fromRequest(ListPaymentMethodsRequest $request): self
    {
        return new self(
            $request->user()->id,
        );
    }
}
