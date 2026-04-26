<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\User;

class CartRepository
{
    public function getOrCreate(User $user): Cart
    {
        return $user->cart()->firstOrCreate([]);
    }

    public function getWithItems(User $user): Cart
    {
        return $user->cart()->with([
            'items.productVariant.product.translations',
            'items.productVariant.images',
            'items.productVariant.attributeValues.translations',
            'items.productVariant.attributeValues.attribute.translations',
        ])->firstOrCreate([]);
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
