<?php

namespace App\DTOs\Admin\Order;

use App\Http\Requests\Admin\Order\CancelOrderRequest;

class CancelOrderDTO
{
    public function __construct(
        public int $storeId,
        public int $orderId,
    ) {}

    public static function fromRequest(CancelOrderRequest $request, int $storeId, int $orderId): self
    {
        return new self(
            storeId: $storeId,
            orderId: $orderId,
        );
    }
}
