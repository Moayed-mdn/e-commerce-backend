<?php

namespace App\DTOs\Admin\Order;

use App\Http\Requests\Admin\Order\UpdateOrderStatusRequest;

class UpdateOrderStatusDTO
{
    public function __construct(
        public int $storeId,
        public int $orderId,
        public string $status,
    ) {}

    public static function fromRequest(UpdateOrderStatusRequest $request, int $storeId, int $orderId): self
    {
        return new self(
            storeId: $storeId,
            orderId: $orderId,
            status: $request->string('status'),
        );
    }
}
