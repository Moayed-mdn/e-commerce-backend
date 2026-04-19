<?php
// app/Http/Controllers/Api/CheckoutController.php

namespace App\Http\Controllers\Api;

use App\Exceptions\Payment\PaymentFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\CreateCheckoutRequest;
use App\Services\CheckoutService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __construct(private CheckoutService $checkoutService)
    {
    }

    /**
     * Create a Stripe Checkout Session.
     * Works for both logged-in users and guests.
     */
    public function createSession(CreateCheckoutRequest $request): JsonResponse
    {
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

        return ApiResponse::success($result);
    }

    /**
     * Retrieve order status after checkout (for the success page).
     */
    public function status(string $sessionId): JsonResponse
    {
        $data = $this->checkoutService->getCheckoutStatus($sessionId);

        return ApiResponse::success($data);
    }
}