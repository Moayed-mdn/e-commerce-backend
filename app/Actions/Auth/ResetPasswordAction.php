<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\ResetPasswordDTO;
use App\Services\AuthService;

class ResetPasswordAction
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function execute(ResetPasswordDTO $dto): void
    {
        $this->authService->resetPassword($dto->toArray());
    }
}
