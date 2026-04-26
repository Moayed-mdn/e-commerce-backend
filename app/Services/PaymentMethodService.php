<?php

namespace App\Services;

use App\DTOs\PaymentMethod\StorePaymentMethodDTO;
use App\Models\PaymentMethod;
use App\Repositories\PaymentMethod\PaymentMethodRepository;
use Illuminate\Database\Eloquent\Collection;

class PaymentMethodService
{
    public function __construct(
        private PaymentMethodRepository $paymentMethodRepository
    ) {}

    public function getUserPaymentMethods(int $userId): Collection
    {
        return $this->paymentMethodRepository->getUserPaymentMethods($userId);
    }

    public function createPaymentMethod(StorePaymentMethodDTO $dto): PaymentMethod
    {
        if ($dto->isDefault) {
            $this->paymentMethodRepository->unsetDefault($dto->userId);
        }

        return $this->paymentMethodRepository->create([
            'user_id' => $dto->userId,
            'provider' => $dto->provider,
            'payment_method_id' => $dto->paymentMethodId,
            'brand' => $dto->brand,
            'last_four' => $dto->lastFour,
            'exp_month' => $dto->expMonth,
            'exp_year' => $dto->expYear,
            'is_default' => $dto->isDefault,
        ]);
    }

    public function deletePaymentMethod(PaymentMethod $paymentMethod): void
    {
        if ($paymentMethod->is_default) {
            $newDefault = $this->paymentMethodRepository->getDefault($paymentMethod->user_id);
            if (!$newDefault) {
                $nextInLine = $this->paymentMethodRepository->getUserPaymentMethods($paymentMethod->user_id)
                    ->where('id', '!=', $paymentMethod->id)
                    ->first();

                if ($nextInLine) {
                    $this->paymentMethodRepository->setAsDefault($paymentMethod->user_id, $nextInLine->id);
                }
            }
        }

        $this->paymentMethodRepository->delete($paymentMethod);
    }

    public function setAsDefault(PaymentMethod $paymentMethod): void
    {
        $this->paymentMethodRepository->setAsDefault(
            $paymentMethod->user_id,
            $paymentMethod->id
        );
    }
}
