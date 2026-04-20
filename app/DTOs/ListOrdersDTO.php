<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Http\Request;

class ListOrdersDTO
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
