<?php
// app/Http/Controllers/Api/OrderController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\FilterOrdersRequest;
use App\Http\Requests\Order\GuestOrderLookupRequest;
use App\Exceptions\NotFoundException;
use App\Exceptions\Order\ReorderException;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Support\ApiResponse;
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

        $query = Order::where('user_id', $user->id)
            ->with([
                'items.productVariant.images',
                'items.productVariant.product.translations',
            ]);

        // ── Filter by status ──
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ── Filter by date range ──
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // ── Paginate (newest first) ──
        $perPage = $request->get('per_page', 1);
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return ApiResponse::paginated(
            paginator: $orders,
            data: OrderResource::collection($orders->items())
        );
    }

    /**
     * Show a single order with full details.
     *
     * GET /orders/{orderNumber}
     */
    public function show(Request $request, string $orderNumber): JsonResponse
    {
        $user = $request->user();

        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->with([
                'items.productVariant.images',
                'items.productVariant.product.translations',
                'items.productVariant.attributeValues.translations',
                'items.productVariant.attributeValues.attribute.translations',
            ])
            ->firstOrFail();

        return ApiResponse::success(new OrderResource($order));
    }

    /**
     * Cancel an order.
     *
     * POST /orders/{orderNumber}/cancel
     */
    public function cancel(Request $request, string $orderNumber): JsonResponse
    {
        $user = $request->user();

        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $cancelledOrder = $this->orderService->cancel($order);

        return ApiResponse::success(
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

        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

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

        return ApiResponse::success($result, $message);
    }

    /**
     * Guest order lookup.
     *
     * POST /orders/guest/lookup
     * Body: { order_number, email }
     */
    public function guestLookup(GuestOrderLookupRequest $request): JsonResponse
    {
        $order = Order::where('order_number', $request->order_number)
            ->where(function ($query) use ($request) {
                $query->where('guest_email', $request->email)
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('email', $request->email);
                    });
            })
            ->with([
                'items.productVariant.images',
                'items.productVariant.product.translations',
                'items.productVariant.attributeValues.translations',
                'items.productVariant.attributeValues.attribute.translations',
            ])
            ->first();

        if (!$order) {
            throw new NotFoundException(__('error.order_not_found'));
        }

        return ApiResponse::success(new OrderResource($order));
    }

    /**
     * Get available status filters with counts.
     *
     * GET /orders/filters
     */
    public function filters(Request $request): JsonResponse
    {
        $user = $request->user();

        $statusCounts = Order::where('user_id', $user->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

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

        return ApiResponse::success($data);
    }
}