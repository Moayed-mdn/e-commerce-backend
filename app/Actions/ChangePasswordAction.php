<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\ChangePasswordDTO;
use App\Services\ProfileService;

class ChangePasswordAction
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function execute(ChangePasswordDTO $dto): void
    {
        $this->profileService->changePassword($dto->user, $dto->password);
    }
}
