<?php

namespace App\Actions\Cart;

use App\DTOs\Cart\ClearCartDTO;
use App\Repositories\Cart\CartRepository;

class ClearCartAction
{
    public function __construct(
        private CartRepository $cartRepository,
    ) {}

    public function execute(ClearCartDTO $dto): void
    {
        $cart = $this->cartRepository->findByUser(
            \App\Models\User::findOrFail($dto->userId),
            $dto->storeId
        );

        if ($cart) {
            $this->cartRepository->deleteByCart($cart);
        }
    }
}