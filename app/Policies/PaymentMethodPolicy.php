<?php
// app/Policies/PaymentMethodPolicy.php
namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PaymentMethodPolicy
{
    public function update(User $user, PaymentMethod $paymentMethod)
    {
        return $user->id === $paymentMethod->user_id;
    }

    public function delete(User $user, PaymentMethod $paymentMethod)
    {
        return $user->id === $paymentMethod->user_id;
    }
}