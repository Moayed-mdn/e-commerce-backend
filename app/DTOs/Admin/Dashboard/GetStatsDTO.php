<?php

namespace App\DTOs\Admin\Dashboard;

use Illuminate\Http\Request;

class GetStatsDTO
{
    public function __construct(
        public int $storeId,
    ) {}

    public static function fromRequest(Request $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
        );
    }
}
