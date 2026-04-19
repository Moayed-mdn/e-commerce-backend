<?php
// app/Http/Controllers/Api/OrderController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\FilterOrdersRequest;
use App\Http\Requests\Order\GuestOrderLookupRequest;
use App\Exceptions\NotFoundException;
use App\Exceptions\Order\ReorderException;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    /**
     * List authenticated user's orders (paginated, filterable).
     *
     * GET /orders
     * Query: ?status=delivered&from_date=2024-01-01&to_date=2024-12-31&per_page=10
     */
    public function index(FilterOrdersRequest $request): JsonResponse
    {
        $user = $request->user();

        $query = $this->orderService->buildUserOrdersQuery($user);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $perPage = $request->get('per_page', 10);
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->paginated($orders, OrderResource::collection($orders->items()));
    }

    /**
     * Show a single order with full details.
     *
     * GET /orders/{orderNumber}
     */
    public function show(Request $request, string $orderNumber): JsonResponse
    {
        $user = $request->user();

        $order = $this->orderService->findOrderByNumberAndUser($orderNumber, $user->id);

        return $this->success(new OrderResource($order));
    }

    /**
     * Cancel an order.
     *
     * POST /orders/{orderNumber}/cancel
     */
    public function cancel(Request $request, string $orderNumber): JsonResponse
    {
        $user = $request->user();

        $order = $this->orderService->findOrderByNumberAndUser($orderNumber, $user->id);

        $cancelledOrder = $this->orderService->cancel($order);

        return $this->success(
            new OrderResource($cancelledOrder->load([
                'items.productVariant.images',
                'items.productVariant.product.translations',
            ])),
            __('services.order_cancelled')
        );
    }

    /**
     * Reorder: add items from a past order into the cart.
     *
     * POST /orders/{orderNumber}/reorder
     */
    public function reorder(Request $request, string $orderNumber): JsonResponse
    {
        $user = $request->user();

        $order = $this->orderService->findOrderByNumberAndUser($orderNumber, $user->id);

        $result = $this->orderService->reorder($order, $user);

        $addedCount = count($result['added']);
        $failedCount = count($result['failed']);

        if ($addedCount === 0) {
            throw new ReorderException(__('services.reorder_items_not_added'), errors: $result);
        }

        $message = trans_choice('services.reorder_items_added', $addedCount, ['count' => $addedCount]);
        if ($failedCount > 0) {
            $message .= ' ' . trans_choice('services.reorder_items_failed', $failedCount, ['count' => $failedCount]);
        }

        return $this->success($result, $message);
    }

    /**
     * Guest order lookup.
     *
     * POST /orders/guest/lookup
     * Body: { order_number, email }
     */
    public function guestLookup(GuestOrderLookupRequest $request): JsonResponse
    {
        $order = $this->orderService->findOrderByGuestLookup($request->order_number, $request->email);

        if (!$order) {
            throw new NotFoundException(__('error.order_not_found'));
        }

        return $this->success(new OrderResource($order));
    }

    /**
     * Get available status filters with counts.
     *
     * GET /orders/filters
     */
    public function filters(Request $request): JsonResponse
    {
        $user = $request->user();

        $statusCounts = $this->orderService->getStatusFilterCounts($user->id);

        $totalOrders = array_sum($statusCounts);

        $data = [
            'statuses' => [
                ['value' => null,          'label' => 'all',        'count' => $totalOrders],
                ['value' => 'pending',     'label' => 'pending',    'count' => $statusCounts['pending'] ?? 0],
                ['value' => 'processing',  'label' => 'processing', 'count' => $statusCounts['processing'] ?? 0],
                ['value' => 'shipped',     'label' => 'shipped',    'count' => $statusCounts['shipped'] ?? 0],
                ['value' => 'delivered',   'label' => 'delivered',  'count' => $statusCounts['delivered'] ?? 0],
                ['value' => 'cancelled',   'label' => 'cancelled',  'count' => $statusCounts['cancelled'] ?? 0],
            ],
        ];

        return $this->success($data);
    }
}