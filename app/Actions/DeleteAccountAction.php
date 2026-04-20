<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\DeleteAccountDTO;
use App\Services\ProfileService;

class DeleteAccountAction
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function execute(DeleteAccountDTO $dto): void
    {
        $this->profileService->deleteAccount($dto->user);
    }
}
