<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Auth\VerifyEmailRequest;

class VerifyEmailDTO
{
    public function __construct(
        public int $id,
        public string $hash,
    ) {}

    public static function fromRequest(VerifyEmailRequest $request): self
    {
        return new self(
            (int) $request->route('id'),
            (string) $request->route('hash'),
        );
    }
}
