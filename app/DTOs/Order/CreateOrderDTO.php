<?php

declare(strict_types=1);

namespace App\DTOs\Order;

use App\Http\Requests\Order\StoreOrderRequest;

class CreateOrderDTO
{
    public function __construct(
        public int $storeId,
        public int $userId,
        public int $shippingAddressId,
        public int $billingAddressId,
        public int $paymentMethodId,
        public string $shippingMethod,
    ) {}

    public static function fromRequest(StoreOrderRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            userId: $request->user()->id,
            shippingAddressId: $request->integer('shipping_address_id'),
            billingAddressId: $request->integer('billing_address_id'),
            paymentMethodId: $request->integer('payment_method_id'),
            shippingMethod: (string) $request->string('shipping_method'),
        );
    }
}
