<?php

namespace App\Repositories\Store;

use App\DTOs\Store\CreateStoreDTO;
use App\DTOs\Store\UpdateStoreDTO;
use App\Models\Store;
use App\Exceptions\Store\StoreNotFoundException;
use Illuminate\Support\Facades\DB;

class StoreRepository
{
    public function create(CreateStoreDTO $dto): Store
    {
        return DB::transaction(function () use ($dto) {
            $store = Store::create([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'owner_id' => $dto->ownerId,
            ]);

            $store->users()->attach($dto->ownerId, ['role' => 'store_admin']);

            return $store;
        });
    }

    public function findById(int $storeId): Store
    {
        $store = Store::find($storeId);

        if (!$store) {
            throw new StoreNotFoundException();
        }

        return $store;
    }

    public function update(UpdateStoreDTO $dto): Store
    {
        $store = $this->findById($dto->storeId);

        $data = [];

        if ($dto->name !== null) {
            $data['name'] = $dto->name;
        }

        if ($dto->slug !== null) {
            $data['slug'] = $dto->slug;
        }

        if ($dto->isActive !== null) {
            $data['is_active'] = $dto->isActive;
        }

        if (!empty($data)) {
            $store->update($data);
        }

        return $store->fresh();
    }
}
