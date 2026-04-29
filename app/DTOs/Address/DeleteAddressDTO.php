<?php

declare(strict_types=1);

namespace App\DTOs\Address;

use Illuminate\Http\Request;

class DeleteAddressDTO
{
    public function __construct(
        public int $storeId,
        public int $addressId,
        public int $userId,
    ) {}

    public static function fromRequest(Request $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            addressId: (int) $request->route('address'),
            userId: $request->user()->id,
        );
    }
}
