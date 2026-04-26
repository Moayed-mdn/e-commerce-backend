<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\GetMeDTO;
use App\Models\User;

class GetMeAction
{
    public function execute(GetMeDTO $dto): User
    {
        return $dto->user;
    }
}
