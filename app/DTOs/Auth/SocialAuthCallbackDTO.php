<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\SocialAuthCallbackRequest;

class SocialAuthCallbackDTO
{
    public function __construct(
        public string $provider,
    ) {}

    public static function fromRequest(SocialAuthCallbackRequest $request): self
    {
        return new self(
            $request->string('provider', 'google')->toString(),
        );
    }
}
