<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\ResendVerificationEmailRequest;

class ResendVerificationEmailDTO
{
    public function __construct(
        public string $email,
        public string $ip,
    ) {}

    public static function fromRequest(ResendVerificationEmailRequest $request): self
    {
        return new self(
            (string) $request->string('email'),
            (string) $request->ip(),
        );
    }
}
