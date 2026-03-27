<?php
// app/Http/Controllers/Api/CheckoutController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\CreateCheckoutRequest;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $checkoutService) {}

    /**
     * Create a Stripe Checkout Session.
     * Works for both logged-in users and guests.
     */
    public function createSession(CreateCheckoutRequest $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user) {
                // Logged-in: read cart from database
                $result = $this->checkoutService->createSessionForUser($user);
            } else {
                // Guest: read cart from request body
                $result = $this->checkoutService->createSessionForGuest(
                    $request->input('items', []),
                    $request->input('email')
                );
            }

            return response()->json([
                'data' => $result,
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json([
                'message' => 'Payment service error. Please try again.',
                'error'   => $e->getMessage(),
            ], 502);
        }
    }

    /**
     * Retrieve order status after checkout (for the success page).
     */
    public function status(string $sessionId): JsonResponse
    {
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            $order = \App\Models\Order::where('stripe_checkout_session_id', $sessionId)
                ->first();

            return response()->json([
                'data' => [
                    'payment_status' => $session->payment_status,  // 'paid', 'unpaid', 'no_payment_required'
                    'order_number'   => $order?->order_number,
                    'order_status'   => $order?->status,
                    'customer_email' => $session->customer_details->email ?? $order?->guest_email,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Could not retrieve checkout status.',
            ], 404);
        }
    }
}