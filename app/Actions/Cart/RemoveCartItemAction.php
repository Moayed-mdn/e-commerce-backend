<?php

namespace App\Actions\Cart;

use App\DTOs\Cart\RemoveCartItemDTO;
use App\Repositories\Cart\CartItemRepository;

class RemoveCartItemAction
{
    public function __construct(
        private CartItemRepository $cartItemRepository,
    ) {}

    public function execute(RemoveCartItemDTO $dto): void
    {
        $item = $this->cartItemRepository->findById($dto->itemId);
        $this->cartItemRepository->delete($item);
    }
}