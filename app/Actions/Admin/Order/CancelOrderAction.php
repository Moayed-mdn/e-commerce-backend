<?php

namespace App\Actions\Admin\Order;

use App\DTOs\Admin\Order\CancelOrderDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Models\Order;
use App\Repositories\Admin\Order\AdminOrderRepository;
use Illuminate\Support\Facades\Auth;

class CancelOrderAction
{
    public function __construct(
        private AdminOrderRepository $repository,
    ) {}

    public function execute(CancelOrderDTO $dto): Order
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        $order = $this->repository->findInStore($dto->orderId, $dto->storeId);
        
        // Logic for cancellation (e.g. inventory restock, status update)
        $order->update(['status' => 'cancelled']);
        
        return $order->fresh();
    }
}
