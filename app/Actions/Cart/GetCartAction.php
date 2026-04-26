<?php

namespace App\Actions\Cart;

use App\DTOs\Cart\GetCartDTO;
use App\Models\Cart;
use App\Models\User;
use App\Repositories\Cart\CartRepository;

class GetCartAction
{
    public function __construct(
        private CartRepository $cartRepository
    ) {}

    public function execute(GetCartDTO $dto): Cart
    {
        $user = User::findOrFail($dto->userId);
        return $this->cartRepository->getWithItems($user);
    }
}