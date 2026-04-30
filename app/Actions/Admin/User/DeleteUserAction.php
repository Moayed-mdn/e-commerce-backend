<?php

namespace App\Actions\Admin\User;

use App\DTOs\Admin\User\DeleteUserDTO;
use App\Models\User;
use App\Repositories\Admin\User\AdminUserRepository;

class DeleteUserAction
{
    public function __construct(
        private AdminUserRepository $repository,
    ) {}

    public function execute(DeleteUserDTO $dto): void
    {
        $user = $this->repository->findInStore($dto->userId, $dto->storeId);

        $this->repository->softDelete($user);
    }
}
