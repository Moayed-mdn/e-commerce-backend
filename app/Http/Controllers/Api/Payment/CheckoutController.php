<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Payment;

use App\Actions\Payment\CreateCheckoutSessionAction;
use App\Actions\Payment\GetCheckoutStatusAction;
use App\DTOs\Payment\CreateCheckoutDTO;
use App\DTOs\Payment\GetCheckoutStatusDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\CreateCheckoutRequest;
use App\Http\Requests\Checkout\GetCheckoutStatusRequest;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __construct(
        private CreateCheckoutSessionAction $createCheckoutSessionAction,
        private GetCheckoutStatusAction $getCheckoutStatusAction,
    ) {}

    public function createSession(CreateCheckoutRequest $request): JsonResponse
    {
        $result = $this->createCheckoutSessionAction->execute(
            CreateCheckoutDTO::fromRequest($request)
        );

        return $this->success($result);
    }

    public function status(GetCheckoutStatusRequest $request, string $sessionId): JsonResponse
    {
        $data = $this->getCheckoutStatusAction->execute(
            GetCheckoutStatusDTO::fromRequest($request, $sessionId)
        );

        return $this->success($data);
    }
}
