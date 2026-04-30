<?php

namespace App\DTOs\Admin\User;

use Illuminate\Http\Request;

class UnblockUserDTO
{
    public function __construct(
        public int $storeId,
        public int $userId,
    ) {}

    public static function fromRequest(Request $request, int $storeId): self
    {
        return new self(
            storeId: $storeId,
            userId: $request->route('user'),
        );
    }
}
