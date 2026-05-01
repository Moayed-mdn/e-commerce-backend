<?php

namespace App\DTOs\Admin\Order;

use App\Http\Requests\Admin\Order\RefundOrderRequest;

class RefundOrderDTO
{
    public function __construct(
        public int $storeId,
        public int $orderId,
    ) {}

    public static function fromRequest(RefundOrderRequest $request, int $storeId, int $orderId): self
    {
        return new self(
            storeId: $storeId,
            orderId: $orderId,
        );
    }
}
