<?php

namespace App\Repositories\Address;

use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;

class AddressRepository
{
    public function getByUser(int $userId, int $storeId, ?string $type = null): Collection
    {
        $query = Address::where('user_id', $userId)
            ->where('store_id', $storeId);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public function find(int $id, int $storeId): Address
    {
        return Address::where('store_id', $storeId)->findOrFail($id);
    }

    public function create(array $data, int $storeId): Address
    {
        $data['store_id'] = $storeId;
        return Address::create($data);
    }

    public function update(Address $address, array $data): Address
    {
        $address->update($data);
        return $address->fresh();
    }

    public function delete(Address $address): void
    {
        $address->delete();
    }

    public function setDefault(int $userId, string $type, int $addressId, int $storeId): void
    {
        Address::where('user_id', $userId)
            ->where('store_id', $storeId)
            ->where('type', $type)
            ->update(['is_default' => false]);

        Address::where('id', $addressId)
            ->where('store_id', $storeId)
            ->update(['is_default' => true]);
    }

    public function unsetDefaultForType(int $userId, string $type, int $excludeId, int $storeId): void
    {
        Address::where('user_id', $userId)
            ->where('store_id', $storeId)
            ->where('type', $type)
            ->where('id', '!=', $excludeId)
            ->update(['is_default' => false]);
    }

    public function getNextDefault(int $userId, string $type, int $excludeId, int $storeId): ?Address
    {
        return Address::where('user_id', $userId)
            ->where('store_id', $storeId)
            ->where('type', $type)
            ->where('id', '!=', $excludeId)
            ->first();
    }
}
