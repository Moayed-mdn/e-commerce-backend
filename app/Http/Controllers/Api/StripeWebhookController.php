<?php
// app/Http/Controllers/Api/StripeWebhookController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(private CheckoutService $checkoutService) {}

    public function handle(Request $request): Response
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = config('services.stripe.webhook_secret');

        // ── 1. Verify webhook signature ────────────────────────
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook: invalid payload');
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            Log::warning('Stripe webhook: invalid signature');
            return response('Invalid signature', 400);
        }

        // ── 2. Handle events ───────────────────────────────────
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;

                // Only process if payment was successful
                if ($session->payment_status === 'paid') {
                    $this->checkoutService->handleCheckoutCompleted($session);
                }
                break;

            case 'checkout.session.async_payment_succeeded':
                // For async payment methods (bank transfers, etc.)
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

        // ── 3. Always return 200 ───────────────────────────────
        return response('OK', 200);
    }
}