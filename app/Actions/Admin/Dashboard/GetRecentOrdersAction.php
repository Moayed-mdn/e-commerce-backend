<?php

namespace App\Actions\Admin\Dashboard;

use App\DTOs\Admin\Dashboard\GetRecentOrdersDTO;
use App\Repositories\Admin\Dashboard\AdminDashboardRepository;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Auth;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;

class GetRecentOrdersAction
{
    public function __construct(
        private AdminDashboardRepository $repository,
    ) {}

    public function execute(GetRecentOrdersDTO $dto): Collection
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        return $this->repository->getRecentOrders(
            storeId: $dto->storeId,
            limit: $dto->limit,
        );
    }
}
