<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\UpdateProfileInfoDTO;
use App\Models\User;
use App\Services\ProfileService;

class UpdateProfileInfoAction
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function execute(UpdateProfileInfoDTO $dto): User
    {
        return $this->profileService->updateProfile($dto->user, [
            'name' => $dto->name,
            'email' => $dto->email,
        ]);
    }
}
