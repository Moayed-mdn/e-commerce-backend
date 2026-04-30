<?php

namespace App\DTOs\Admin\Dashboard;

use Illuminate\Http\Request;

class GetTopProductsDTO
{
    public function __construct(
        public int $storeId,
        public int $limit = 10,
    ) {}

    public static function fromRequest(Request $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            limit: $request->integer('limit', 10),
        );
    }
}
