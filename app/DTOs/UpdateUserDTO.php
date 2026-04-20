<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\User;

class UpdateUserDTO
{
    public function __construct(
        public User $user,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $phone = null,
    ) {}

    public static function fromRequest(UpdateProfileRequest $request): self
    {
        return new self(
            $request->user(),
            $request->filled('name') ? (string) $request->string('name') : null,
            $request->filled('email') ? (string) $request->string('email') : null,
            $request->filled('phone') ? (string) $request->string('phone') : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ], fn($value) => $value !== null);
    }
}
