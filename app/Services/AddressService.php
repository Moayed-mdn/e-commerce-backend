<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Address\StoreAddressDTO;
use App\DTOs\Address\UpdateAddressDTO;
use App\Models\Address;
use App\Repositories\Address\AddressRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AddressService
{
    public function __construct(
        private AddressRepository $addressRepository,
    ) {}

    public function getUserAddresses(int $storeId, int $userId, ?string $type = null): Collection
    {
        return $this->addressRepository->getByUser($userId, $storeId, $type);
    }

    public function storeAddress(int $storeId, StoreAddressDTO $dto): Address
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
            $this->addressRepository->setDefault($dto->userId, $dto->type, 0, $storeId);
        }

        $address = $this->addressRepository->create($data, $storeId);

        if ($dto->isDefault) {
            $this->addressRepository->setDefault($dto->userId, $dto->type, $address->id, $storeId);
        }

        return $address;
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
            $this->addressRepository->unsetDefaultForType($address->user_id, $address->type, $address->id, $dto->storeId);
        }

        $updated = $this->addressRepository->update($address, $data);

        if ($dto->isDefault) {
            $this->addressRepository->setDefault($address->user_id, $address->type, $address->id, $dto->storeId);
        }

        return $updated;
    }

    public function deleteAddress(Address $address, int $storeId): bool
    {
        if ($address->is_default) {
            $newDefault = $this->addressRepository->getNextDefault(
                $address->user_id,
                $address->type,
                $address->id,
                $storeId
            );

            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return $this->addressRepository->delete($address);
    }

    public function setAsDefault(Address $address, int $storeId): void
    {
        $this->addressRepository->setDefault(
            $address->user_id,
            $address->type,
            $address->id,
            $storeId
        );
    }
}
