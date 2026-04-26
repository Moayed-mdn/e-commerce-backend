<?php

declare(strict_types=1);

namespace App\DTOs\Order;

use Illuminate\Http\Request;

class CancelOrderDTO
{
    public function __construct(
        public int $orderId,
        public int $userId,
    ) {}

    public static function fromRequest(\App\Http\Requests\Order\CancelOrderRequest $request): self
    {
        return new self(
            (int) $request->route('orderNumber'),
            $request->user()->id,
        );
    }
}
