<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Address\SetDefaultAddressRequest;

class SetDefaultAddressDTO
{
    public function __construct(
        public int $addressId,
        public int $userId,
    ) {}

    public static function fromRequest(SetDefaultAddressRequest $request): self
    {
        return new self(
            (int) $request->route('address'),
            $request->user()->id,
        );
    }
}
