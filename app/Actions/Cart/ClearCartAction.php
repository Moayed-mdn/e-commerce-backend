<?php

namespace App\Actions\Cart;

use App\DTOs\ClearCartDTO;
use App\Repositories\CartRepository;

class ClearCartAction
{
    public function __construct(
        private CartRepository $cartRepository,
    ) {}

    public function execute(ClearCartDTO $dto): void
    {
        $cart = $this->cartRepository->findByUser(
            \App\Models\User::findOrFail($dto->userId)
        );

        if ($cart) {
            $this->cartRepository->deleteByCart($cart);
        }
    }
}