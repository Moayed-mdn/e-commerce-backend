<?php

namespace App\Actions\Store;

use App\DTOs\Store\UpdateStoreDTO;
use App\Models\Store;
use App\Repositories\Store\StoreRepository;

class UpdateStoreAction
{
    public function __construct(
        private StoreRepository $storeRepository,
    ) {}

    public function execute(UpdateStoreDTO $dto): Store
    {
        return $this->storeRepository->update($dto);
    }
}
