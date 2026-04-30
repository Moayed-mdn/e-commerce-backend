<?php

namespace App\Actions\Cart;

use App\DTOs\Cart\AddToCartDTO;
use App\Exceptions\Order\OutOfStockException;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Cart\CartItemRepository;
use App\Repositories\Product\ProductVariantRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\DB;

class AddToCartAction
{
    public function __construct(
        private CartRepository $cartRepository,
        private CartItemRepository $cartItemRepository,
        private ProductVariantRepository $productVariantRepository,
        private UserRepository $userRepository,
    ) {}

    public function execute(AddToCartDTO $dto): \App\Models\Cart
    {
        return DB::transaction(function () use ($dto) {
            $user = $this->userRepository->findOrFail($dto->userId);

            $cart = $this->cartRepository->getOrCreate(
                $user,
                $dto->storeId,
            );

            $variant = $this->productVariantRepository->findWithLock($dto->productVariantId, $dto->storeId);

            if (!$variant->is_active || $variant->quantity < $dto->quantity) {
                throw new OutOfStockException(__('cart.variant_not_available'));
            }

            $existingItem = $this->cartItemRepository->findByCartAndVariant($cart, $dto->productVariantId);

            if ($existingItem) {
                $newQty = $existingItem->quantity + $dto->quantity;

                if ($variant->quantity < $newQty) {
                    throw new OutOfStockException(__('cart.not_enough_stock'));
                }

                $this->cartItemRepository->updateQuantity($existingItem, $newQty);
            } else {
                $this->cartItemRepository->create($cart, $dto->productVariantId, $dto->quantity, (float) $variant->price);
            }

            return $cart->load(['items.productVariant']);
        });
    }
}