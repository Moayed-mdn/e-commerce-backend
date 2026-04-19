<?php

namespace App\Actions;

use App\DTOs\RemoveCartItemDTO;
use App\Repositories\CartItemRepository;

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
