<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User;

class UserRepository
{
    public function findOrFail(int $userId): User
    {
        return User::findOrFail($userId);
    }
}
