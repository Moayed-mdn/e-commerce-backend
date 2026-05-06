<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Models\User;

class LoginResultDTO
{
    public function __construct(
        public User $user,
        public string $token,
    ) {}
}
