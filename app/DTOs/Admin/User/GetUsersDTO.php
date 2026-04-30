<?php

namespace App\DTOs\Admin\User;

use Illuminate\Http\Request;

class GetUsersDTO
{
    public function __construct(
        public int $storeId,
        public ?int $page = 1,
        public ?int $perPage = 15,
        public ?string $search = null,
        public ?string $role = null,
    ) {}

    public static function fromRequest(Request $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            page: $request->integer('page', 1),
            perPage: $request->integer('per_page', 15),
            search: $request->string('search', ''),
            role: $request->string('role', ''),
        );
    }
}
