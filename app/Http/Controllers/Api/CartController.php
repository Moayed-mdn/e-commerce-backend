<?php

namespace App\Http\Controllers\Api;

use App\Actions\AddToCartAction;
use App\Actions\ClearCartAction;
use App\Actions\RemoveCartItemAction;
use App\Actions\UpdateCartItemAction;
use App\DTOs\AddToCartDTO;
use App\DTOs\ClearCartDTO;
use App\DTOs\RemoveCartItemDTO;
use App\DTOs\UpdateCartItemDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddItemRequest;
use App\Http\Requests\Cart\UpdateItemRequest;
use App\Http\Resources\CartResource;

class CartController extends Controller
{
    public function __construct(
        private AddToCartAction $addToCartAction,
        private UpdateCartItemAction $updateCartItemAction,
        private RemoveCartItemAction $removeCartItemAction,
        private ClearCartAction $clearCartAction,
    ) {}

    public function show(): \Illuminate\Http\JsonResponse
    {
        $cart = auth()->user()->cart()->with([
            'items.productVariant.product.translations',
            'items.productVariant.images',
            'items.productVariant.attributeValues.translations',
            'items.productVariant.attributeValues.attribute.translations',
        ])->firstOrCreate([]);

        return $this->success(new CartResource($cart));
    }

    public function addItem(AddItemRequest $request): \Illuminate\Http\JsonResponse
    {
        $cart = $this->addToCartAction->execute(
            AddToCartDTO::fromRequest($request)
        );

        return $this->success(new CartResource($cart));
    }

    public function updateItem(UpdateItemRequest $request, int $itemId): \Illuminate\Http\JsonResponse
    {
        $dto = UpdateCartItemDTO::fromRequest($request, $itemId);
        $this->updateCartItemAction->execute($dto);

        return $this->show();
    }

    public function removeItem(int $itemId): \Illuminate\Http\JsonResponse
    {
        $dto = RemoveCartItemDTO::fromRequest(request(), $itemId);
        $this->removeCartItemAction->execute($dto);

        return $this->show();
    }

    public function clear(): \Illuminate\Http\JsonResponse
    {
        $dto = ClearCartDTO::fromRequest(request());
        $this->clearCartAction->execute($dto);

        return $this->success(null, __('cart.cleared'));
    }
}