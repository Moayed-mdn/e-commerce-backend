<?php

namespace App\DTOs\Store;

use App\Http\Requests\Store\UpdateStoreRequest;

class UpdateStoreDTO
{
    public function __construct(
        public int $storeId,
        public ?string $name,
        public ?string $slug,
        public ?bool $isActive,
    ) {}

    public static function fromRequest(UpdateStoreRequest $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            name: $request->string('name', null),
            slug: $request->string('slug', null),
            isActive: $request->boolean('is_active', null),
        );
    }
}
