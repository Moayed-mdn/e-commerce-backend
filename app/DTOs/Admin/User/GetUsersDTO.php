<?php

declare(strict_types=1);

namespace App\DTOs\Admin\User;

use Illuminate\Http\Request;

class GetUsersDTO
{
    public function __construct(
        public int $storeId,
        public int $perPage,
        public int $page,
    ) {}

    public static function fromRequest(Request $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            perPage: $request->integer('per_page', 15),
            page: $request->integer('page', 1),
        );
    }
}
