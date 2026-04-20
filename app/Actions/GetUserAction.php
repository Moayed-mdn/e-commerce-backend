<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\GetUserDTO;
use App\Models\User;

class GetUserAction
{
    public function execute(GetUserDTO $dto): User
    {
        return $dto->user;
    }
}
