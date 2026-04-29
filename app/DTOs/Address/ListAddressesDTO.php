<?php

declare(strict_types=1);

namespace App\DTOs\Address;

use App\Http\Requests\Address\ListAddressesRequest;

class ListAddressesDTO
{
    public function __construct(
        public int $storeId,
        public int $userId,
        public ?string $type = null,
    ) {}

    public static function fromRequest(ListAddressesRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            userId: $request->user()->id,
            type: $request->input('type')
        );
    }
}
