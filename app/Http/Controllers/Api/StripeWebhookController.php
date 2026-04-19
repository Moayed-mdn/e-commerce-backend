<?php
// app/Http/Controllers/Api/StripeWebhookController.php

namespace App\Http\Controllers\Api;

use App\Exceptions\Payment\StripeWebhookException;
use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(private CheckoutService $checkoutService) {}

    public function handle(Request $request): JsonResponse
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            throw new StripeWebhookException('Invalid payload');
        } catch (SignatureVerificationException $e) {
            throw new StripeWebhookException('Invalid signature');
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                if ($session->payment_status === 'paid') {
                    $this->checkoutService->handleCheckoutCompleted($session);
                }
                break;

            case 'checkout.session.async_payment_succeeded':
                $session = $event->data->object;
                $this->checkoutService->handleCheckoutCompleted($session);
                break;

            case 'checkout.session.async_payment_failed':
                $session = $event->data->object;
                $this->checkoutService->handleSessionExpired($session);
                break;

            case 'checkout.session.expired':
                $session = $event->data->object;
                $this->checkoutService->handleSessionExpired($session);
                break;

            default:
                Log::info("Stripe webhook: unhandled event type {$event->type}");
        }

        return ApiResponse::success(null, 'Webhook handled.');
    }
}