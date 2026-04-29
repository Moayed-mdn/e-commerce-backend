<?php

namespace App\DTOs\Store;

use App\Http\Requests\Store\CreateStoreRequest;

class CreateStoreDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public int $ownerId,
    ) {}

    public static function fromRequest(CreateStoreRequest $request): self
    {
        return new self(
            name: $request->string('name'),
            slug: $request->string('slug'),
            ownerId: $request->user()->id,
        );
    }
}
