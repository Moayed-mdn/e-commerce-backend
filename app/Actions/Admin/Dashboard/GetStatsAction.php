<?php

namespace App\Actions\Admin\Dashboard;

use App\DTOs\Admin\Dashboard\GetStatsDTO;
use App\Repositories\Admin\Dashboard\AdminDashboardRepository;

use Illuminate\Support\Facades\Auth;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;

class GetStatsAction
{
    public function __construct(
        private AdminDashboardRepository $repository,
    ) {}

    public function execute(GetStatsDTO $dto): array
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        return [
            'revenue' => [
                'total'      => $this->repository->getTotalRevenue($dto->storeId),
                'this_month' => $this->repository->getRevenueThisMonth($dto->storeId),
                'last_month' => $this->repository->getRevenueLastMonth($dto->storeId),
            ],
            'orders' => [
                'total'      => $this->repository->getTotalOrders($dto->storeId),
                'this_month' => $this->repository->getOrdersThisMonth($dto->storeId),
                'last_month' => $this->repository->getOrdersLastMonth($dto->storeId),
            ],
            'customers' => [
                'total' => $this->repository->getTotalCustomers($dto->storeId),
            ],
        ];
    }
}
