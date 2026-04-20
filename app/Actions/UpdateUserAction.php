<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\UpdateUserDTO;
use App\Models\User;
use App\Services\ProfileService;

class UpdateUserAction
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function execute(UpdateUserDTO $dto): User
    {
        return $this->profileService->updateProfile($dto->user, $dto->toArray());
    }
}
