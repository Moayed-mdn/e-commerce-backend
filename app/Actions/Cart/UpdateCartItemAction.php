<?php

namespace App\Actions\Cart;

use App\DTOs\UpdateCartItemDTO;
use App\Exceptions\Order\OutOfStockException;
use App\Repositories\CartItemRepository;
use App\Repositories\ProductVariantRepository;
use Illuminate\Support\Facades\DB;

class UpdateCartItemAction
{
    public function __construct(
        private CartItemRepository $cartItemRepository,
        private ProductVariantRepository $productVariantRepository,
    ) {}

    public function execute(UpdateCartItemDTO $dto): void
    {
        DB::transaction(function () use ($dto) {
            $item = $this->cartItemRepository->findById($dto->itemId);
            $variant = $item->productVariant;

            if (!$variant->is_active || $variant->quantity < $dto->quantity) {
                throw new OutOfStockException(__('cart.variant_not_available'));
            }

            $this->cartItemRepository->updateQuantity($item, $dto->quantity);
        });
    }
}