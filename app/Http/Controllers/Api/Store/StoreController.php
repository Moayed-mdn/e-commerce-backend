<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CreateStoreRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Http\Resources\Store\StoreResource;
use App\Actions\Store\CreateStoreAction;
use App\Actions\Store\UpdateStoreAction;
use App\DTOs\Store\CreateStoreDTO;
use App\DTOs\Store\UpdateStoreDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    public function __construct(
        private CreateStoreAction $createStoreAction,
        private UpdateStoreAction $updateStoreAction,
    ) {}

    public function create(CreateStoreRequest $request): JsonResponse
    {
        $dto = CreateStoreDTO::fromRequest($request);
        $store = $this->createStoreAction->execute($dto);

        return $this->success(new StoreResource($store), 'Store created successfully', 201);
    }

    public function show(Request $request, int $store): JsonResponse
    {
        $storeModel = app('currentStore');

        return $this->success(new StoreResource($storeModel), 'Store retrieved successfully');
    }

    public function update(UpdateStoreRequest $request, int $store): JsonResponse
    {
        $dto = UpdateStoreDTO::fromRequest($request, $store);
        $storeModel = $this->updateStoreAction->execute($dto);

        return $this->success(new StoreResource($storeModel), 'Store updated successfully');
    }
}
