<?php

namespace App\Actions\Admin\Product;

use App\DTOs\Admin\Product\ListProductsDTO;
use App\Repositories\Admin\Product\AdminProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Auth;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;

class ListProductsAction
{
    public function __construct(
        private AdminProductRepository $repository,
    ) {}

    public function execute(ListProductsDTO $dto): LengthAwarePaginator
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        return $this->repository->listForStore(
            storeId: $dto->storeId,
            search: $dto->search,
            status: $dto->status,
            perPage: $dto->perPage,
        );
    }
}
