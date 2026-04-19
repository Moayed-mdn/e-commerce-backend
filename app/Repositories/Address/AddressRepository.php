<?php

declare(strict_types=1);

namespace App\Repositories\Address;

use App\Models\Address;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AddressRepository
{
    public function getUserAddresses(int $userId, ?string $type = null): LengthAwarePaginator
    {
        $query = Address::query()
            ->where('user_id', $userId);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->latest('is_default')
            ->latest('id')
            ->paginate(20);
    }

    public function findById(int $id): ?Address
    {
        return Address::find($id);
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

    public function delete(Address $address): bool
    {
        return $address->delete();
    }

    public function setAsDefault(Address $address): void
    {
        Address::query()
            ->where('user_id', $address->user_id)
            ->where('type', $address->type)
            ->update(['is_default' => false]);

        $address->update(['is_default' => true]);
    }

    public function getUserDefaultAddress(int $userId, string $type): ?Address
    {
        return Address::query()
            ->where('user_id', $userId)
            ->where('type', $type)
            ->where('is_default', true)
            ->first();
    }

    public function setAsDefaultForType(int $userId, string $type): void
    {
        Address::query()
            ->where('user_id', $userId)
            ->where('type', $type)
            ->update(['is_default' => false]);
    }
}
