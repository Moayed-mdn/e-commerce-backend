<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\GetProfileDTO;
use App\Models\User;

class GetProfileAction
{
    public function execute(GetProfileDTO $dto): User
    {
        return $dto->user;
    }
}
