<?php

declare(strict_types=1);

namespace App\Actions\PaymentMethod;

use App\DTOs\PaymentMethod\ListPaymentMethodsDTO;
use App\Services\PaymentMethodService;
use Illuminate\Database\Eloquent\Collection;

class ListPaymentMethodsAction
{
    public function __construct(
        private PaymentMethodService $paymentMethodService,
    ) {}

    public function execute(ListPaymentMethodsDTO $dto): Collection
    {
        return $this->paymentMethodService->getUserPaymentMethods($dto->userId);
    }
}
