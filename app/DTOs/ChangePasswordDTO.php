<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\User\ChangePasswordRequest;
use App\Models\User;

class ChangePasswordDTO
{
    public function __construct(
        public User $user,
        public string $password,
    ) {}

    public static function fromRequest(ChangePasswordRequest $request): self
    {
        return new self(
            $request->user(),
            (string) $request->string('password'),
        );
    }
}
