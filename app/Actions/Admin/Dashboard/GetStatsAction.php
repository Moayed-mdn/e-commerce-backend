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
            'total_revenue'      => (float) $this->repository->getTotalRevenue($dto->storeId),
            'total_orders'       => (int)   $this->repository->getTotalOrders($dto->storeId),
            'total_customers'    => (int)   $this->repository->getTotalCustomers($dto->storeId),
            'total_products'     => (int)   $this->repository->getTotalProducts($dto->storeId),
            'revenue_change'     => 0.0,
            'orders_change'      => 0.0,
            'customers_change'   => 0.0,
            'products_change'    => 0.0,
        ];
    }
}
