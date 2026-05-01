<?php

namespace App\Actions\Admin\Order;

use App\DTOs\Admin\Order\ListOrdersDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Repositories\Admin\Order\AdminOrderRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ListOrdersAction
{
    public function __construct(
        private AdminOrderRepository $repository,
    ) {}

    public function execute(ListOrdersDTO $dto): LengthAwarePaginator
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
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
