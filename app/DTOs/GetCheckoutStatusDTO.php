<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Checkout\GetCheckoutStatusRequest;

class GetCheckoutStatusDTO
{
    public function __construct(
        public string $sessionId,
    ) {}

    public static function fromRequest(GetCheckoutStatusRequest $request, string $sessionId): self
    {
        return new self($sessionId);
    }
}
