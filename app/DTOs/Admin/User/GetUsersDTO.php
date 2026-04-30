<?php

namespace App\DTOs\Admin\User;

use Illuminate\Http\Request;

class GetUsersDTO
{
    public function __construct(
        public int $storeId,
        public int $perPage,
        public ?string $search,
    ) {}

    public static function fromRequest(Request $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            perPage: $request->integer('per_page', 15),
            search: $request->string('search')->whenNotEmpty(fn($s) => $s)->value(),
        );
    }
}
