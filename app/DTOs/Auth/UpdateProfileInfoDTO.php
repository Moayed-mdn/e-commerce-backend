<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\Profile\UpdateInfoRequest;
use App\Models\User;

class UpdateProfileInfoDTO
{
    public function __construct(
        public User $user,
        public string $name,
        public string $email,
    ) {}

    public static function fromRequest(UpdateInfoRequest $request): self
    {
        return new self(
            $request->user(),
            (string) $request->string('name'),
            (string) $request->string('email'),
        );
    }
}
