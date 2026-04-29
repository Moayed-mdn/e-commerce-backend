<?php

declare(strict_types=1);

namespace App\Actions\Order;

use App\DTOs\Order\ListOrdersDTO;
use App\Repositories\Order\OrderRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListOrdersAction
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    public function execute(ListOrdersDTO $dto): LengthAwarePaginator
    {
        return $this->orderRepository->getUserOrders($dto->userId, $dto->storeId);
    }
}
