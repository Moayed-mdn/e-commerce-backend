<?php

declare(strict_types=1);

namespace App\Actions\Payment;

use App\DTOs\Payment\StripeWebhookDTO;
use App\Exceptions\Payment\StripeWebhookException;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class HandleStripeWebhookAction
{
    public function __construct(
        private CheckoutService $checkoutService
    ) {}

    public function execute(StripeWebhookDTO $dto): void
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($dto->payload, $dto->sigHeader, $secret);
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
    }
}
