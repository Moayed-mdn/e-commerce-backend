<?php

declare(strict_types=1);

namespace App\Actions\PaymentMethod;

use App\DTOs\PaymentMethod\StorePaymentMethodDTO;
use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;

class StorePaymentMethodAction
{
    public function __construct(
        private PaymentMethodService $paymentMethodService,
    ) {}

    public function execute(StorePaymentMethodDTO $dto): PaymentMethod
    {
        return $this->paymentMethodService->createPaymentMethod($dto);
    }
}
