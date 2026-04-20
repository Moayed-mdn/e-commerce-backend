<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Profile\GetProfileRequest;
use App\Models\User;

class GetProfileDTO
{
    public function __construct(
        public User $user,
    ) {}

    public static function fromRequest(GetProfileRequest $request): self
    {
        return new self($request->user());
    }
}
