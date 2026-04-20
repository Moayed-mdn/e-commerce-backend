<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CancelOrderAction;
use App\Actions\CreateOrderAction;
use App\Actions\GetOrderAction;
use App\Actions\ListOrdersAction;
use App\DTOs\CancelOrderDTO;
use App\DTOs\CreateOrderDTO;
use App\DTOs\GetOrderDTO;
use App\DTOs\ListOrdersDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CancelOrderRequest;
use App\Http\Requests\Order\GetOrderRequest;
use App\Http\Requests\Order\ListOrdersRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        private ListOrdersAction $listOrdersAction,
        private GetOrderAction $getOrderAction,
        private CreateOrderAction $createOrderAction,
        private CancelOrderAction $cancelOrderAction,
    ) {}

    public function index(ListOrdersRequest $request): JsonResponse
    {
        $orders = $this->listOrdersAction->execute(
            ListOrdersDTO::fromRequest($request)
        );

        return $this->paginated(
            $orders,
            OrderResource::collection($orders)
        );
    }

    public function show(GetOrderRequest $request): JsonResponse
    {
        $order = $this->getOrderAction->execute(
            GetOrderDTO::fromRequest($request)
        );

        return $this->success(new OrderResource($order));
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->createOrderAction->execute(
            CreateOrderDTO::fromRequest($request)
        );

        return $this->success(new OrderResource($order), __('order.created'), 201);
    }

    public function cancel(CancelOrderRequest $request): JsonResponse
    {
        $order = $this->cancelOrderAction->execute(
            CancelOrderDTO::fromRequest($request)
        );

        return $this->success(new OrderResource($order), __('order.cancelled'));
    }
}
