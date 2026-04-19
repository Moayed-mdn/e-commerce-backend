<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\User;

class CartRepository
{
    public function getOrCreate(User $user): Cart
    {
        return $user->cart()->firstOrCreate([]);
    }

    public function findById(int $id): Cart
    {
        return Cart::findOrFail($id);
    }

    public function findByUser(User $user): ?Cart
    {
        return $user->cart;
    }

    public function deleteByCart(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
