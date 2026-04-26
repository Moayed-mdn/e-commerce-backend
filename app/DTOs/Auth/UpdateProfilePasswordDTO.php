<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Models\User;

class UpdateProfilePasswordDTO
{
    public function __construct(
        public User $user,
        public string $password,
    ) {}

    public static function fromRequest(UpdatePasswordRequest $request): self
    {
        return new self(
            $request->user(),
            (string) $request->string('password'),
        );
    }
}
