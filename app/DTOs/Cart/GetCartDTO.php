<?php

namespace App\DTOs\Cart;

use Illuminate\Http\Request;

class GetCartDTO
{
    public function __construct(
        public int $userId,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->user()->id,
        );
    }
}
