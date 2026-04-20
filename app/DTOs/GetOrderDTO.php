<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

class GetOrderDTO
{
    public function __construct(
        public int $orderId,
        public int $userId,
    ) {}

    public static function fromRequest(\App\Http\Requests\Order\GetOrderRequest $request): self
    {
        return new self(
            (int) $request->route('orderNumber'),
            $request->user()->id,
        );
    }
}
