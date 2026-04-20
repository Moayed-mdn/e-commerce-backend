<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Password\ResetPasswordRequest;

class ResetPasswordDTO
{
    public function __construct(
        public string $token,
        public string $email,
        public string $password,
        public string $passwordConfirmation,
    ) {}

    public static function fromRequest(ResetPasswordRequest $request): self
    {
        return new self(
            (string) $request->string('token'),
            (string) $request->string('email'),
            (string) $request->string('password'),
            (string) $request->string('password_confirmation'),
        );
    }

    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->passwordConfirmation,
        ];
    }
}
