<?php

namespace App\Actions\Address;

use App\Models\Address;
use App\Repositories\Address\AddressRepository;

class DeleteAddressAction
{
    public function __construct(
        private AddressRepository $addressRepository
    ) {}

    public function execute(Address $address): void
    {
        if ($address->is_default) {
            $newDefault = $this->addressRepository->getNextDefault(
                $address->user_id,
                $address->type,
                $address->id
            );

            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $this->addressRepository->delete($address);
    }
}
