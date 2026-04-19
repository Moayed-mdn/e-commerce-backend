<?php

namespace App\Services;

use App\Actions\DeleteAddressAction;
use App\Actions\SetDefaultAddressAction;
use App\Actions\StoreAddressAction;
use App\Actions\UpdateAddressAction;
use App\DTOs\StoreAddressDTO;
use App\DTOs\UpdateAddressDTO;
use App\Models\Address;
use App\Repositories\AddressRepository;
use Illuminate\Database\Eloquent\Collection;

class AddressService
{
    public function __construct(
        private AddressRepository $addressRepository,
        private StoreAddressAction $storeAction,
        private UpdateAddressAction $updateAction,
        private DeleteAddressAction $deleteAction,
        private SetDefaultAddressAction $setDefaultAction,
    ) {}

    public function getUserAddresses(int $userId, ?string $type = null): Collection
    {
        return $this->addressRepository->getByUser($userId, $type);
    }

    public function storeAddress(StoreAddressDTO $dto): Address
    {
        return $this->storeAction->execute($dto);
    }

    public function updateAddress(Address $address, UpdateAddressDTO $dto): Address
    {
        return $this->updateAction->execute($address, $dto);
    }

    public function deleteAddress(Address $address): void
    {
        $this->deleteAction->execute($address);
    }

    public function setAsDefault(Address $address): void
    {
        $this->setDefaultAction->execute($address);
    }
}
