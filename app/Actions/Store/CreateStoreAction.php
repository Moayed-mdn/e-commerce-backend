<?php

namespace App\Actions\Store;

use App\DTOs\Store\CreateStoreDTO;
use App\Models\Store;
use App\Repositories\Store\StoreRepository;

class CreateStoreAction
{
    public function __construct(
        private StoreRepository $storeRepository,
    ) {}

    public function execute(CreateStoreDTO $dto): Store
    {
        return $this->storeRepository->create($dto);
    }
}
