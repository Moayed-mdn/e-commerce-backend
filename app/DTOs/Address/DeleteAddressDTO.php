<?php

declare(strict_types=1);

namespace App\DTOs\Address;

use Illuminate\Http\Request;

class DeleteAddressDTO
{
    public function __construct(
        public int $addressId,
        public int $userId,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            (int) $request->route('address'),
            $request->user()->id,
        );
    }
}
