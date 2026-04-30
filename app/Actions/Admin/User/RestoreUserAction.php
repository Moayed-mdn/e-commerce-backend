<?php

namespace App\Actions\Admin\User;

use App\DTOs\Admin\User\RestoreUserDTO;
use App\Models\User;
use App\Repositories\Admin\User\AdminUserRepository;

class RestoreUserAction
{
    public function __construct(
        private AdminUserRepository $repository,
    ) {}

    public function execute(RestoreUserDTO $dto): User
    {
        return $this->repository->restore($dto->userId, $dto->storeId);
    }
}
