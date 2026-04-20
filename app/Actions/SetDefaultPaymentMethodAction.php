<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\SetDefaultPaymentMethodDTO;
use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;
use Illuminate\Auth\Access\AuthorizationException;

class SetDefaultPaymentMethodAction
{
    public function __construct(
        private PaymentMethodService $paymentMethodService,
    ) {}

    public function execute(SetDefaultPaymentMethodDTO $dto): void
    {
        $paymentMethod = PaymentMethod::findOrFail($dto->paymentMethodId);

        if ($paymentMethod->user_id !== $dto->userId) {
            throw new AuthorizationException(__('error.unauthorized_payment_method_access'));
        }

        $this->paymentMethodService->setAsDefault($paymentMethod);
    }
}
