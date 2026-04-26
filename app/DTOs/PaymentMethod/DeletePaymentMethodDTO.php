<?php

declare(strict_types=1);

namespace App\DTOs\PaymentMethod;

use App\Http\Requests\PaymentMethod\DeletePaymentMethodRequest;

class DeletePaymentMethodDTO
{
    public function __construct(
        public int $paymentMethodId,
        public int $userId,
    ) {}

    public static function fromRequest(DeletePaymentMethodRequest $request): self
    {
        return new self(
            (int) $request->route('id'),
            $request->user()->id,
        );
    }
}
