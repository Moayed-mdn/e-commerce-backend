<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Auth\LogoutRequest;
use App\Models\User;

class LogoutDTO
{
    public function __construct(
        public User $user,
    ) {}

    public static function fromRequest(LogoutRequest $request): self
    {
        return new self($request->user());
    }
}
