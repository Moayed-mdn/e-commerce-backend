<?php

declare(strict_types=1);

namespace App\Actions\Payment;

use App\DTOs\Payment\CreateCheckoutDTO;
use App\Services\CheckoutService;

class CreateCheckoutSessionAction
{
    public function __construct(
        private CheckoutService $checkoutService,
    ) {}

    public function execute(CreateCheckoutDTO $dto): array
    {
        if ($dto->user) {
            return $this->checkoutService->createSessionForUser($dto->user);
        }

        return $this->checkoutService->createSessionForGuest(
            $dto->items,
            $dto->email
        );
    }
}
