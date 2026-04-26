<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\User\GetProfileRequest;
use App\Models\User;

class GetUserDTO
{
    public function __construct(
        public User $user,
    ) {}

    public static function fromRequest(GetProfileRequest $request): self
    {
        return new self($request->user());
    }
}
