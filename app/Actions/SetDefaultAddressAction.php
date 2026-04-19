<?php

namespace App\Actions;

use App\Models\Address;
use App\Repositories\AddressRepository;

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
