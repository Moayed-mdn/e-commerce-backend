<?php

namespace App\Actions\Admin\Product;

use App\DTOs\Admin\Product\GetProductDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Models\Product;
use App\Repositories\Admin\Product\AdminProductRepository;
use Illuminate\Support\Facades\Auth;

class GetProductAction
{
    public function __construct(
        private AdminProductRepository $repository,
    ) {}

    public function execute(GetProductDTO $dto): Product
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        return $this->repository->findInStore($dto->productId, $dto->storeId)
            ->load(['category', 'variants.attributeValues', 'translations', 'media', 'tags']);
    }
}
