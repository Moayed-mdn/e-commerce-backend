<?php

declare(strict_types=1);

namespace App\Services\Order;

use App\Exceptions\Order\OrderCancellationException;
use App\Exceptions\Payment\PaymentFailedException;
use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use App\Repositories\Order\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Refund;
use Stripe\Stripe;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Get user orders paginated.
     */
    public function getUserOrders(int $userId): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->orderRepository->getUserOrders($userId);
    }

    /**
     * Load order relations for display.
     */
    public function loadOrderRelations(Order $order): void
    {
        $order->load([
            'items.productVariant.product.images',
            'shippingAddress',
            'billingAddress',
            'paymentMethod'
        ]);
    }

    /**
     * Create an order from cart.
     */
    public function createOrderFromCart(int $userId, array $data): Order
    {
        return DB::transaction(function () use ($userId, $data) {
            $user = User::findOrFail($userId);
            $cart = $user->cart;

            if (!$cart || $cart->items->isEmpty()) {
                throw new \App\Exceptions\System\UnprocessableContentException(__('error.cart_empty'));
            }

            $shippingAddress = $user->addresses()->findOrFail($data['shipping_address_id']);
            $billingAddress = $user->addresses()->findOrFail($data['billing_address_id']);
            $paymentMethod = $user->paymentMethods()->findOrFail($data['payment_method_id']);

            $subtotal = $cart->total;
            $shippingAmount = $this->calculateShipping($data['shipping_method'], $cart);
            $taxAmount = $this->calculateTax($subtotal, $shippingAddress);
            $total = $subtotal + $shippingAmount + $taxAmount;

            $order = Order::create([
                'user_id' => $user->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
                'payment_method_id' => $paymentMethod->id,
                'subtotal' => $subtotal,
                'shipping_amount' => $shippingAmount,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'shipping_method' => $data['shipping_method'],
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            foreach ($cart->items as $cartItem) {
                $variant = $cartItem->productVariant;

                $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'quantity' => $cartItem->quantity,
                    'attributes' => $variant->attributes->pluck('attribute_value', 'attribute_name')
                ]);

                $variant->decrement('quantity', $cartItem->quantity);
            }

            $cart->items()->delete();
            $order->markAsPaid();

            return $order;
        });
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder(Order $order): Order
    {
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

    /**
     * Calculate shipping amount.
     */
    private function calculateShipping(string $shippingMethod, Cart $cart): float
    {
        $rates = [
            'standard' => 5.00,
            'express' => 15.00,
            'overnight' => 25.00,
        ];

        return $rates[$shippingMethod] ?? $rates['standard'];
    }

    /**
     * Calculate tax amount.
     */
    private function calculateTax(float $subtotal, $address): float
    {
        $taxRates = [
            'CA' => 0.0825,
            'NY' => 0.08875,
            'TX' => 0.0825,
        ];

        $rate = $taxRates[$address->state] ?? 0.08;
        return $subtotal * $rate;
    }
}