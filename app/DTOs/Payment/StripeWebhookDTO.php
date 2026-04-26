<?php

declare(strict_types=1);

namespace App\DTOs\Payment;

use Illuminate\Http\Request;

class StripeWebhookDTO
{
    public function __construct(
        public string $payload,
        public ?string $sigHeader,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->getContent(),
            $request->header('Stripe-Signature'),
        );
    }
}
