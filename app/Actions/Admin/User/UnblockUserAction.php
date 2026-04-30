<?php

namespace App\Actions\Admin\User;

use App\DTOs\Admin\User\UnblockUserDTO;
use App\Models\User;
use App\Repositories\Admin\User\AdminUserRepository;

class UnblockUserAction
{
    public function __construct(
        private AdminUserRepository $repository,
    ) {}

    public function execute(UnblockUserDTO $dto): User
    {
        $user = $this->repository->findInStore($dto->userId, $dto->storeId);

        return $this->repository->unblock($user);
    }
}
