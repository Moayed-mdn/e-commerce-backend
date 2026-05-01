<?php

namespace App\Actions\Admin\Dashboard;

use App\DTOs\Admin\Dashboard\GetRecentOrdersDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Repositories\Admin\Dashboard\AdminDashboardRepository;
use Illuminate\Support\Collection;

class GetRecentOrdersAction
{
    public function __construct(
        private AdminDashboardRepository $repository,
    ) {}

    public function execute(GetRecentOrdersDTO $dto): Collection
    {
        $authUser = auth()->user();
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
