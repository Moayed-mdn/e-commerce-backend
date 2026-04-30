<?php

declare(strict_types=1);

namespace App\DTOs\Admin\User;

use Illuminate\Http\Request;

class DeleteUserDTO
{
    public function __construct(
        public int $storeId,
        public int $userId,
    ) {}

    public static function fromRequest(Request $request, int $storeId, int $userId): self
    {
        return new self(
            storeId: $storeId,
            userId: $userId,
        );
    }
}
