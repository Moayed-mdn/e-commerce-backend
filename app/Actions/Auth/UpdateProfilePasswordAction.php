<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\UpdateProfilePasswordDTO;
use App\Services\ProfileService;

class UpdateProfilePasswordAction
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function execute(UpdateProfilePasswordDTO $dto): void
    {
        $this->profileService->changePassword($dto->user, $dto->password);
    }
}
