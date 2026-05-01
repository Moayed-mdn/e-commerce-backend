<?php

namespace App\Actions\Admin\Product;

use App\DTOs\Admin\Product\DeleteProductDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Repositories\Admin\Product\AdminProductRepository;
use Illuminate\Support\Facades\Auth;

class DeleteProductAction
{
    public function __construct(
        private AdminProductRepository $repository,
    ) {}

    public function execute(DeleteProductDTO $dto): void
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        $product = $this->repository->findInStore($dto->productId, $dto->storeId);
        $this->repository->softDelete($product);
    }
}
