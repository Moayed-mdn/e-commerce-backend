<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderService;


class OrderController extends Controller
{

    public function __construct(
        private OrderService $orderService,
    ) {}

    public function index()
    {
        $orders = $this->orderService->getUserOrders(auth()->id());

        return new OrderCollection($orders);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $this->orderService->loadOrderRelations($order);

        return $this->success(new OrderResource($order));
    }

    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->createOrderFromCart(
            auth()->id(),
            $request->validated()
        );

        return $this->success(new OrderResource($order), __('order.created'), 201);
    }

    public function cancel(Order $order)
    {
        $this->authorize('update', $order);

        $this->orderService->cancelOrder($order);

        return $this->success(null, __('order.cancelled'));
    }
}