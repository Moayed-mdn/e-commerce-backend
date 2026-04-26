<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\Password\SendResetLinkRequest;

class SendResetLinkDTO
{
    public function __construct(
        public string $email,
    ) {}

    public static function fromRequest(SendResetLinkRequest $request): self
    {
        return new self(
            (string) $request->string('email'),
        );
    }
}
