<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\GetUserDTO;
use App\Models\User;

class GetUserAction
{
    public function execute(GetUserDTO $dto): User
    {
        return $dto->user;
    }
}
