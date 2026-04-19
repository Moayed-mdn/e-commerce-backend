<?php
// app/Http/Controllers/Api/OrderController.php
namespace App\Http\Controllers\Api;

use App\Exceptions\Order\OrderCancellationException;
use App\Exceptions\System\UnprocessableContentException;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['items', 'shippingAddress', 'billingAddress', 'paymentMethod'])
            ->latest()
            ->paginate(10);

        return new OrderCollection($orders);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load([
            'items.productVariant.product.images',
            'shippingAddress',
            'billingAddress',
            'paymentMethod'
        ]);

        return ApiResponse::success(new OrderResource($order));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'shipping_method' => 'required|string|max:255',
        ]);

        return DB::transaction(function () use ($request) {
            $user = auth()->user();
            $cart = $user->cart;

            if (!$cart || $cart->items->isEmpty()) {
                throw new UnprocessableContentException('Cart is empty');
            }

            $shippingAddress = Address::where('id', $request->shipping_address_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $billingAddress = Address::where('id', $request->billing_address_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $paymentMethod = PaymentMethod::where('id', $request->payment_method_id)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $subtotal = $cart->total;
            $shippingAmount = $this->calculateShipping($request->shipping_method, $cart);
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
                'shipping_method' => $request->shipping_method,
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

            $order->load(['items', 'shippingAddress', 'billingAddress']);

            return ApiResponse::success(new OrderResource($order), 'Order created successfully', 201);
        });
    }

    public function cancel(Order $order)
    {
        $this->authorize('update', $order);

        if (!$order->canBeCancelled()) {
            throw new OrderCancellationException('Order cannot be cancelled');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $item->productVariant->increment('quantity', $item->quantity);
            }

            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'refunded'
            ]);
        });

        return ApiResponse::success(null, 'Order cancelled successfully');
    }

    private function calculateShipping($shippingMethod, $cart)
    {
        $rates = [
            'standard' => 5.00,
            'express' => 15.00,
            'overnight' => 25.00,
        ];

        return $rates[$shippingMethod] ?? $rates['standard'];
    }

    private function calculateTax($subtotal, $address)
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