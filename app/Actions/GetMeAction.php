<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\GetMeDTO;
use App\Models\User;

class GetMeAction
{
    public function execute(GetMeDTO $dto): User
    {
        return $dto->user;
    }
}
