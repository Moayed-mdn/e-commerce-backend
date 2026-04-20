<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Order\StoreOrderRequest;

class CreateOrderDTO
{
    public function __construct(
        public int $userId,
        public int $shippingAddressId,
        public int $billingAddressId,
        public int $paymentMethodId,
        public string $shippingMethod,
    ) {}

    public static function fromRequest(StoreOrderRequest $request): self
    {
        return new self(
            $request->user()->id,
            $request->integer('shipping_address_id'),
            $request->integer('billing_address_id'),
            $request->integer('payment_method_id'),
            (string) $request->string('shipping_method'),
        );
    }
}
