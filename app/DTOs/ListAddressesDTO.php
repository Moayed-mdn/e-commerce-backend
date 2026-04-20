<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Address\ListAddressesRequest;

class ListAddressesDTO
{
    public function __construct(
        public int $userId,
        public ?string $type = null,
    ) {}

    public static function fromRequest(ListAddressesRequest $request): self
    {
        return new self(
            $request->user()->id,
            $request->input('type')
        );
    }
}
