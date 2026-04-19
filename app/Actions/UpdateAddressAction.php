<?php

namespace App\Actions;

use App\DTOs\UpdateAddressDTO;
use App\Models\Address;
use App\Repositories\AddressRepository;

class UpdateAddressAction
{
    public function __construct(
        private AddressRepository $addressRepository
    ) {}

    public function execute(Address $address, UpdateAddressDTO $dto): Address
    {
        if ($dto->isDefault) {
            $this->addressRepository->unsetDefaultForType(
                $address->user_id,
                $address->type,
                $address->id
            );
        }

        $updated = $this->addressRepository->update($address, [
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
            $this->addressRepository->setDefault($address->user_id, $address->type, $address->id);
        }

        return $updated;
    }
}
