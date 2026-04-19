<?php

namespace App\Services;

use App\Exceptions\Order\OutOfStockException;
use App\Models\Cart;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreate($user): Cart
    {
        return $user->cart()->firstOrCreate([]);
    }

    public function addItem(Cart $cart, int $variantId, int $quantity): void
    {
        DB::transaction(function () use ($cart, $variantId, $quantity) {

            $variant = ProductVariant::lockForUpdate()->findOrFail($variantId);

            if (!$variant->is_active || $variant->quantity < $quantity) {
                throw new OutOfStockException(__('services.variant_not_available'));
            }

            $item = $cart->items()
                ->where('product_variant_id', $variantId)
                ->first();

            if ($item) {
                $newQty = $item->quantity + $quantity;

                if ($variant->quantity < $newQty) {
                    throw new OutOfStockException(__('services.not_enough_stock'));
                }

                $item->update(['quantity' => $newQty]);
            } else {
                $cart->items()->create([
                    'product_variant_id' => $variantId,
                    'quantity' => $quantity
                ]);
            }
        });
    }

    public function updateItem(Cart $cart, int $itemId, int $quantity): void
    {
        DB::transaction(function () use ($cart, $itemId, $quantity) {

            $item = $cart->items()->lockForUpdate()->findOrFail($itemId);
            $variant = $item->productVariant;

            if (!$variant->is_active || $variant->quantity < $quantity) {
                throw new OutOfStockException(__('services.variant_not_available'));
            }

            $item->update(['quantity' => $quantity]);
        });
    }

    public function removeItem(Cart $cart, int $itemId): void
    {
        $cart->items()->where('id', $itemId)->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }
}