<?php

namespace App\Actions\Address;

use App\Models\Address;
use App\Repositories\Address\AddressRepository;

class DeleteAddressAction
{
    public function __construct(
        private AddressRepository $addressRepository
    ) {}

    public function execute(Address $address, int $storeId): void
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

        $this->addressRepository->delete($address);
    }
}
