<?php

namespace App\DTOs\PaymentMethod;

use App\Http\Requests\PaymentMethod\StorePaymentMethodRequest;

class StorePaymentMethodDTO
{
    public function __construct(
        public string $provider,
        public string $paymentMethodId,
        public string $brand,
        public string $lastFour,
        public int $expMonth,
        public int $expYear,
        public bool $isDefault,
        public int $userId,
    ) {}

    public static function fromRequest(StorePaymentMethodRequest $request): self
    {
        return new self(
            provider: $request->provider,
            paymentMethodId: $request->payment_method_id,
            brand: $request->brand,
            lastFour: $request->last_four,
            expMonth: $request->exp_month,
            expYear: $request->exp_year,
            isDefault: $request->boolean('is_default', false),
            userId: $request->user()->id,
        );
    }
}
