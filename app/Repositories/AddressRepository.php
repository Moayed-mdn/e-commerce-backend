<?php

namespace App\Repositories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;

class AddressRepository
{
    public function getByUser(int $userId, ?string $type = null): Collection
    {
        $query = Address::where('user_id', $userId);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public function find(int $id): Address
    {
        return Address::findOrFail($id);
    }

    public function create(array $data): Address
    {
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

    public function setDefault(int $userId, string $type, int $addressId): void
    {
        Address::where('user_id', $userId)
            ->where('type', $type)
            ->update(['is_default' => false]);

        Address::where('id', $addressId)->update(['is_default' => true]);
    }

    public function unsetDefaultForType(int $userId, string $type, int $excludeId): void
    {
        Address::where('user_id', $userId)
            ->where('type', $type)
            ->where('id', '!=', $excludeId)
            ->update(['is_default' => false]);
    }

    public function getNextDefault(int $userId, string $type, int $excludeId): ?Address
    {
        return Address::where('user_id', $userId)
            ->where('type', $type)
            ->where('id', '!=', $excludeId)
            ->first();
    }
}
