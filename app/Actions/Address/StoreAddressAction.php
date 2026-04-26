<?php

declare(strict_types=1);

namespace App\Actions\Address;

use App\DTOs\Address\StoreAddressDTO;
use App\Models\Address;
use App\Services\AddressService;

class StoreAddressAction
{
    public function __construct(
        private AddressService $addressService,
    ) {}

    public function execute(StoreAddressDTO $dto): Address
    {
        return $this->addressService->storeAddress($dto);
    }
}
