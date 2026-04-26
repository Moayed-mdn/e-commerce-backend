<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Address\StoreAddressDTO;
use App\DTOs\Address\UpdateAddressDTO;
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

    public function storeAddress(StoreAddressDTO $dto): Address
    {
        $data = [
            'user_id' => $dto->userId,
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'company' => $dto->company,
            'phone' => $dto->phone,
            'country' => $dto->country,
            'state' => $dto->state,
            'city' => $dto->city,
            'address_line_1' => $dto->addressLine1,
            'address_line_2' => $dto->addressLine2,
            'postal_code' => $dto->postalCode,
            'type' => $dto->type,
            'is_default' => $dto->isDefault,
        ];

        if ($dto->isDefault) {
            $this->addressRepository->setAsDefaultForType($dto->userId, $dto->type);
        }

        return $this->addressRepository->create($data);
    }

    public function updateAddress(Address $address, UpdateAddressDTO $dto): Address
    {
        $data = [
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'company' => $dto->company,
            'phone' => $dto->phone,
            'country' => $dto->country,
            'state' => $dto->state,
            'city' => $dto->city,
            'address_line_1' => $dto->addressLine1,
            'address_line_2' => $dto->addressLine2,
            'postal_code' => $dto->postalCode,
            'is_default' => $dto->isDefault,
        ];

        if ($dto->isDefault) {
            $this->addressRepository->setAsDefaultForType($address->user_id, $address->type);
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
