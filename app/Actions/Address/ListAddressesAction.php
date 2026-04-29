<?php

declare(strict_types=1);

namespace App\Actions\Address;

use App\DTOs\Address\ListAddressesDTO;
use App\Services\AddressService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListAddressesAction
{
    public function __construct(
        private AddressService $addressService,
    ) {}

    public function execute(ListAddressesDTO $dto): LengthAwarePaginator
    {
        return $this->addressService->getUserAddresses($dto->storeId, $dto->userId, $dto->type);
    }
}
