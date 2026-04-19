<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethod\ListPaymentMethodsRequest;
use App\Http\Requests\PaymentMethod\StorePaymentMethodRequest;
use App\Http\Requests\PaymentMethod\SetDefaultPaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Services\PaymentMethod\PaymentMethodService;
use App\DTOs\StorePaymentMethodDTO;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function __construct(
        private PaymentMethodService $paymentMethodService,
    ) {}

    public function index(ListPaymentMethodsRequest $request)
    {
        $paymentMethods = $this->paymentMethodService->getUserPaymentMethods($request->user()->id);

        return $this->success(PaymentMethodResource::collection($paymentMethods), 'Payment methods retrieved successfully');
    }

    public function store(StorePaymentMethodRequest $request)
    {
        $dto = StorePaymentMethodDTO::fromRequest($request);
        
        $paymentMethod = $this->paymentMethodService->storePaymentMethod(
            $dto,
            $request->user()->id
        );

        return $this->success(new PaymentMethodResource($paymentMethod), 'Payment method added successfully', 201);
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $this->authorize('delete', $paymentMethod);

        $this->paymentMethodService->deletePaymentMethod($paymentMethod);

        return $this->success(null, 'Payment method deleted successfully');
    }

    public function setDefault(SetDefaultPaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        $this->authorize('update', $paymentMethod);

        $this->paymentMethodService->setAsDefault($paymentMethod);

        return $this->success(null, 'Payment method set as default successfully');
    }
}