<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\LogoutDTO;

class LogoutUserAction
{
    public function execute(LogoutDTO $dto): void
    {
        $dto->user->currentAccessToken()->delete();
    }
}
