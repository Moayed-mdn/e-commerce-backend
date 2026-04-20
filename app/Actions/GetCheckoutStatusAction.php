<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\GetCheckoutStatusDTO;
use App\Services\CheckoutService;

class GetCheckoutStatusAction
{
    public function __construct(
        private CheckoutService $checkoutService,
    ) {}

    public function execute(GetCheckoutStatusDTO $dto): array
    {
        return $this->checkoutService->getCheckoutStatus($dto->sessionId);
    }
}
