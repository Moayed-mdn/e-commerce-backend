<?php

declare(strict_types=1);

namespace App\Actions\Order;

use App\DTOs\CancelOrderDTO;
use App\Exceptions\Order\OrderCancellationException;
use App\Exceptions\Payment\PaymentFailedException;
use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Refund;
use Stripe\Stripe;

class CancelOrderAction
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function execute(CancelOrderDTO $dto): Order
    {
        $order = $this->orderRepository->findById($dto->orderId);

        if (!$order || $order->user_id !== $dto->userId) {
            throw new \Illuminate\Auth\Access\AuthorizationException(__('error.unauthorized_order_access'));
        }

        if (!$order->canBeCancelled()) {
            throw new OrderCancellationException(__('order.cannot_be_cancelled'));
        }

        return DB::transaction(function () use ($order) {
            // Restore stock
            $this->orderRepository->restoreProductVariants($order);

            // Issue Stripe refund (if paid)
            if ($order->payment_status === 'paid' && $order->payment_intent_id) {
                try {
                    Refund::create([
                        'payment_intent' => $order->payment_intent_id,
                    ]);

                    $order = $this->orderRepository->cancel($order);

                    Log::info('Order refunded via Stripe', [
                        'order_id' => $order->id,
                        'payment_intent_id' => $order->payment_intent_id,
                    ]);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    Log::error('Stripe refund failed', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);

                    throw new PaymentFailedException(__('payment.failed'));
                }
            } else {
                // Not paid yet — just cancel
                $order = $this->orderRepository->cancel($order);
            }

            return $order;
        });
    }
}
