<?php

declare(strict_types=1);

namespace App\DTOs\Order;

use Illuminate\Http\Request;

class CancelOrderDTO
{
    public function __construct(
        public int $storeId,
        public int $orderId,
        public int $userId,
    ) {}

    public static function fromRequest(\App\Http\Requests\Order\CancelOrderRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            orderId: (int) $request->route('orderNumber'),
            userId: $request->user()->id,
        );
    }
}
