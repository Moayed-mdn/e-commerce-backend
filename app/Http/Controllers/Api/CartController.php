<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddItemRequest;
use App\Http\Requests\Cart\UpdateItemRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    public function show()
    {
        $cart = $this->cartService->getOrCreate(auth()->user());

        $cartResource = new CartResource($cart->loadMissing([
            'items.productVariant.product.translations',                // ✅ translated name + slug
            'items.productVariant.images',                              // ✅ variant image
            'items.productVariant.attributeValues.translations',        // ✅ "Red", "أحمر"
            'items.productVariant.attributeValues.attribute.translations', // ✅ "Color", "اللون"
        ]));

        return ApiResponse::success($cartResource);
    }

    public function addItem(AddItemRequest $request)
    {
        $cart = $this->cartService->getOrCreate(auth()->user());

        $this->cartService->addItem(
            $cart,
            $request->product_variant_id,
            $request->quantity
        );

        return $this->show();
    }

    public function updateItem(UpdateItemRequest $request, $itemId)
    {
        $cart = $this->cartService->getOrCreate(auth()->user());

        $this->cartService->updateItem(
            $cart,
            $itemId,
            $request->quantity
        );

        return $this->show();
    }

    public function removeItem($itemId)
    {
        $cart = $this->cartService->getOrCreate(auth()->user());

        $this->cartService->removeItem($cart, $itemId);

        return $this->show();
    }

    public function clear(): JsonResponse
    {
        $cart = $this->cartService->getOrCreate(auth()->user());

        $this->cartService->clear($cart);

        return ApiResponse::success(null, __('cart.cleared'));
    }
}