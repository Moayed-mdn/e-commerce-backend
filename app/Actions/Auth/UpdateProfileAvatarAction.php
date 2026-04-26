<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\UpdateProfileAvatarDTO;
use App\Services\ProfileService;

class UpdateProfileAvatarAction
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function execute(UpdateProfileAvatarDTO $dto): string
    {
        return $this->profileService->updateAvatar($dto->user, $dto->avatar);
    }
}
