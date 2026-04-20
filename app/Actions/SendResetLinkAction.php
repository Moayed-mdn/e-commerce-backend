<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\SendResetLinkDTO;
use App\Services\AuthService;

class SendResetLinkAction
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function execute(SendResetLinkDTO $dto): void
    {
        $this->authService->sendResetLink(['email' => $dto->email]);
    }
}
