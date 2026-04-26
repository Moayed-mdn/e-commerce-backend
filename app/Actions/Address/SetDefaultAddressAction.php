<?php

namespace App\Actions\Address;

use App\Models\Address;
use App\Repositories\Address\AddressRepository;

class SetDefaultAddressAction
{
    public function __construct(
        private AddressRepository $addressRepository
    ) {}

    public function execute(Address $address): void
    {
        $this->addressRepository->setDefault(
            $address->user_id,
            $address->type,
            $address->id
        );
    }
}
