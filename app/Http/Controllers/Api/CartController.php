<?php

namespace App\Http\Controllers\Api;

use App\Actions\AddToCartAction;
use App\Actions\ClearCartAction;
use App\Actions\GetCartAction;
use App\Actions\RemoveCartItemAction;
use App\Actions\UpdateCartItemAction;
use App\DTOs\AddToCartDTO;
use App\DTOs\ClearCartDTO;
use App\DTOs\GetCartDTO;
use App\DTOs\RemoveCartItemDTO;
use App\DTOs\UpdateCartItemDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddItemRequest;
use App\Http\Requests\Cart\ClearRequest;
use App\Http\Requests\Cart\RemoveItemRequest;
use App\Http\Requests\Cart\UpdateItemRequest;
use App\Http\Resources\CartResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private GetCartAction $getCartAction,
        private AddToCartAction $addToCartAction,
        private UpdateCartItemAction $updateCartItemAction,
        private RemoveCartItemAction $removeCartItemAction,
        private ClearCartAction $clearCartAction,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $cart = $this->getCartAction->execute(
            GetCartDTO::fromRequest($request)
        );

        return $this->success(new CartResource($cart));
    }

    public function addItem(AddItemRequest $request): JsonResponse
    {
        $cart = $this->addToCartAction->execute(
            AddToCartDTO::fromRequest($request)
        );

        return $this->success(new CartResource($cart));
    }

    public function updateItem(UpdateItemRequest $request): JsonResponse
    {
        $this->updateCartItemAction->execute(
            UpdateCartItemDTO::fromRequest($request)
        );

        return $this->show($request);
    }

    public function removeItem(RemoveItemRequest $request): JsonResponse
    {
        $this->removeCartItemAction->execute(
            RemoveCartItemDTO::fromRequest($request)
        );

        return $this->show($request);
    }

    public function clear(ClearRequest $request): JsonResponse
    {
        $this->clearCartAction->execute(
            ClearCartDTO::fromRequest($request)
        );

        return $this->success(null, __('cart.cleared'));
    }
}
