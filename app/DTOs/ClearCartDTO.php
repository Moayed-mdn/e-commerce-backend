<?php

namespace App\DTOs;

class ClearCartDTO
{
    public function __construct(
        public int $userId,
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            $request->user()->id,
        );
    }
}
