<?php
// app/Services/OrderService.php

namespace App\Services;

use App\Exceptions\Order\OrderCancellationException;
use App\Exceptions\Payment\PaymentFailedException;
use App\Models\Cart;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Refund;
use Stripe\Stripe;

class OrderService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Cancel an order.
     * - Only pending/processing orders can be cancelled.
     * - Restores stock.
     * - Issues Stripe refund if payment was made.
     */
    public function cancel(Order $order): Order
    {
        if (!$order->canBeCancelled()) {
            throw new OrderCancellationException(__('order.cannot_be_cancelled'));
        }

        return DB::transaction(function () use ($order) {

            // ── 1. Restore stock ───────────────────────────────
            $order->load('items');

            foreach ($order->items as $item) {
                $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);

                if ($variant) {
                    $variant->increment('quantity', $item->quantity);
                }
            }

            // ── 2. Issue Stripe refund (if paid) ───────────────
            if ($order->payment_status === 'paid' && $order->payment_intent_id) {
                try {
                    Refund::create([
                        'payment_intent' => $order->payment_intent_id,
                    ]);

                    $order->update([
                        'status' => 'cancelled',
                        'payment_status' => 'refunded',
                    ]);

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
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => $order->payment_status === 'paid' ? 'refunded' : 'cancelled',
                ]);
            }

            return $order->fresh();
        });
    }

    /**
     * Reorder: add all items from a past order back into the user's cart.
     * Returns a report of what was added and what failed.
     */
    public function reorder(Order $order, User $user): array
    {
        $order->load('items');

        $cartService = app(CartService::class);
        $cart = $cartService->getOrCreate($user);

        $added = [];
        $failed = [];
        $locale = app()->getLocale();

        foreach ($order->items as $item) {
            $variant = ProductVariant::with([
                'product.translations',
            ])->find($item->product_variant_id);

            // Get translated product name for error messages
            $productName = $item->product_name; // Fallback to stored name

            if ($variant) {
                $translation = $variant->product->translations
                    ->where('locale', $locale)->first()
                    ?? $variant->product->translations->first();

                $productName = $translation?->name ?? $item->product_name;
            }

            // ── Check: variant still exists? ──
            if (!$variant) {
                $failed[] = [
                    'product_name' => $productName,
                    'reason'       => 'no_longer_available',
                ];
                continue;
            }

            // ── Check: variant still active? ──
            if (!$variant->is_active) {
                $failed[] = [
                    'product_name' => $productName,
                    'reason'       => 'no_longer_active',
                ];
                continue;
            }

            // ── Check: has enough stock? ──
            $requestedQty = $item->quantity;
            $availableQty = $variant->quantity;

            if ($availableQty <= 0) {
                $failed[] = [
                    'product_name' => $productName,
                    'reason'       => 'out_of_stock',
                ];
                continue;
            }

            // If not enough stock, add what's available
            $qtyToAdd = min($requestedQty, $availableQty);

            try {
                $cartService->addItem($cart, $variant->id, $qtyToAdd);

                $addedItem = [
                    'product_name' => $productName,
                    'quantity'     => $qtyToAdd,
                ];

                if ($qtyToAdd < $requestedQty) {
                    $addedItem['note'] = 'partial';
                    $addedItem['requested'] = $requestedQty;
                    $addedItem['available'] = $availableQty;
                }

                // Flag if price changed
                if ((float) $variant->price !== (float) $item->unit_price) {
                    $addedItem['price_changed'] = true;
                    $addedItem['old_price'] = (float) $item->unit_price;
                    $addedItem['new_price'] = (float) $variant->price;
                }

                $added[] = $addedItem;
            } catch (\Exception $e) {
                $failed[] = [
                    'product_name' => $productName,
                    'reason'       => 'add_failed',
                    'message'      => $e->getMessage(),
                ];
            }
        }

        return [
            'added'  => $added,
            'failed' => $failed,
        ];
    }
}