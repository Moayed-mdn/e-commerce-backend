<?php

namespace App\DTOs\Admin\Order;

use App\Http\Requests\Admin\Order\GetOrderRequest;

class GetOrderDTO
{
    public function __construct(
        public int $storeId,
        public int $orderId,
    ) {}

    public static function fromRequest(GetOrderRequest $request, int $storeId, int $orderId): self
    {
        return new self(
            storeId: $storeId,
            orderId: $orderId,
        );
    }
}
