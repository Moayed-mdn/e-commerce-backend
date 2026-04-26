<?php

namespace App\Repositories\PaymentMethod;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;

class PaymentMethodRepository
{
    public function getUserPaymentMethods(int $userId): Collection
    {
        return PaymentMethod::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(array $data): PaymentMethod
    {
        return PaymentMethod::create($data);
    }

    public function find(int $id): ?PaymentMethod
    {
        return PaymentMethod::find($id);
    }

    public function delete(PaymentMethod $paymentMethod): void
    {
        $paymentMethod->delete();
    }

    public function setAsDefault(int $userId, int $paymentMethodId): void
    {
        PaymentMethod::where('user_id', $userId)->update(['is_default' => false]);
        PaymentMethod::where('id', $paymentMethodId)->update(['is_default' => true]);
    }

    public function unsetDefault(int $userId, int $excludeId = null): void
    {
        $query = PaymentMethod::where('user_id', $userId);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        $query->update(['is_default' => false]);
    }

    public function getDefault(int $userId): ?PaymentMethod
    {
        return PaymentMethod::where('user_id', $userId)
            ->where('is_default', true)
            ->first();
    }
}
