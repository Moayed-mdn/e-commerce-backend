<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\StoreAddressDTO;
use App\DTOs\UpdateAddressDTO;
use App\Models\Address;
use App\Repositories\Address\AddressRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AddressService
{
    public function __construct(
        private AddressRepository $addressRepository,
    ) {}

    public function getUserAddresses(int $userId, ?string $type = null): LengthAwarePaginator
    {
        return $this->addressRepository->getUserAddresses($userId, $type);
    }

    public function storeAddress(StoreAddressDTO $dto, int $userId): Address
    {
        $data = [
            'user_id' => $userId,
            'name' => $dto->name,
            'phone' => $dto->phone,
            'country' => $dto->country,
            'state' => $dto->state,
            'city' => $dto->city,
            'address_line1' => $dto->addressLine1,
            'address_line2' => $dto->addressLine2,
            'postal_code' => $dto->postalCode,
            'type' => $dto->type,
            'is_default' => $dto->isDefault,
        ];

        if ($dto->isDefault) {
            $this->addressRepository->setAsDefaultForType($userId, $dto->type);
        }

        return $this->addressRepository->create($data);
    }

    public function updateAddress(Address $address, UpdateAddressDTO $dto): Address
    {
        $data = [
            'name' => $dto->name,
            'phone' => $dto->phone,
            'country' => $dto->country,
            'state' => $dto->state,
            'city' => $dto->city,
            'address_line1' => $dto->addressLine1,
            'address_line2' => $dto->addressLine2,
            'postal_code' => $dto->postalCode,
            'type' => $dto->type,
        ];

        if ($dto->isDefault !== null && $dto->isDefault) {
            $this->addressRepository->setAsDefaultForType($address->user_id, $dto->type);
        }

        return $this->addressRepository->update($address, $data);
    }

    public function deleteAddress(Address $address): bool
    {
        return $this->addressRepository->delete($address);
    }

    public function setAsDefault(Address $address): void
    {
        $this->addressRepository->setAsDefault($address);
    }
}
