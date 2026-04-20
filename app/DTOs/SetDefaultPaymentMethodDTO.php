<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\PaymentMethod\SetDefaultPaymentMethodRequest;

class SetDefaultPaymentMethodDTO
{
    public function __construct(
        public int $paymentMethodId,
        public int $userId,
    ) {}

    public static function fromRequest(SetDefaultPaymentMethodRequest $request): self
    {
        return new self(
            (int) $request->route('id'),
            $request->user()->id,
        );
    }
}
