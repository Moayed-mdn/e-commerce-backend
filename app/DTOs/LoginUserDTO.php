<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Auth\LoginUserRequest;

class LoginUserDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(LoginUserRequest $request): self
    {
        return new self(
            (string) $request->string('email'),
            (string) $request->string('password'),
        );
    }
}
