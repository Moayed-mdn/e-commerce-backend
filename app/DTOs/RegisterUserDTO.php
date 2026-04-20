<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Auth\RegistgerUserRequest;

class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(RegistgerUserRequest $request): self
    {
        return new self(
            (string) $request->string('name'),
            (string) $request->string('email'),
            (string) $request->string('password'),
        );
    }
}
