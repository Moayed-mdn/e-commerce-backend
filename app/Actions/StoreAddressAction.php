<?php

namespace App\Actions;

use App\DTOs\StoreAddressDTO;
use App\Models\Address;
use App\Repositories\AddressRepository;

class StoreAddressAction
{
    public function __construct(
        private AddressRepository $addressRepository
    ) {}

    public function execute(StoreAddressDTO $dto): Address
    {
        if ($dto->isDefault) {
            $this->addressRepository->unsetDefaultForType(
                $dto->userId,
                $dto->type,
                0 // Will be set after creation
            );
        }

        $address = $this->addressRepository->create([
            'user_id' => $dto->userId,
            'type' => $dto->type,
            'first_name' => $dto->firstName,
            'last_name' => $dto->lastName,
            'company' => $dto->company,
            'address_line_1' => $dto->addressLine1,
            'address_line_2' => $dto->addressLine2,
            'city' => $dto->city,
            'state' => $dto->state,
            'postal_code' => $dto->postalCode,
            'country' => $dto->country,
            'phone' => $dto->phone,
            'is_default' => $dto->isDefault,
        ]);

        if ($dto->isDefault) {
            $this->addressRepository->setDefault($dto->userId, $dto->type, $address->id);
        }

        return $address;
    }
}
