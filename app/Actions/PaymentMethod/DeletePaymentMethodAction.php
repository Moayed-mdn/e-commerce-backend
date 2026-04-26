<?php

declare(strict_types=1);

namespace App\Actions\PaymentMethod;

use App\DTOs\PaymentMethod\DeletePaymentMethodDTO;
use App\Models\PaymentMethod;
use App\Services\PaymentMethodService;
use Illuminate\Auth\Access\AuthorizationException;

class DeletePaymentMethodAction
{
    public function __construct(
        private PaymentMethodService $paymentMethodService,
    ) {}

    public function execute(DeletePaymentMethodDTO $dto): void
    {
        $paymentMethod = PaymentMethod::findOrFail($dto->paymentMethodId);

        if ($paymentMethod->user_id !== $dto->userId) {
            throw new AuthorizationException(__('error.unauthorized_payment_method_access'));
        }

        $this->paymentMethodService->deletePaymentMethod($paymentMethod);
    }
}
