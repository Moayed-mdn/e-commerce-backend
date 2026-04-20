<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\GetProfileDTO;
use App\Models\User;

class GetProfileAction
{
    public function execute(GetProfileDTO $dto): User
    {
        return $dto->user;
    }
}
