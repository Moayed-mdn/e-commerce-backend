<?php

namespace App\Actions\Admin\User;

use App\DTOs\Admin\User\DeleteUserDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Models\User;
use App\Repositories\Admin\User\AdminUserRepository;

class DeleteUserAction
{
    public function __construct(
        private AdminUserRepository $repository,
    ) {}

    public function execute(DeleteUserDTO $dto): void
    {
        $authUser = auth()->user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        $user = $this->repository->findInStore($dto->userId, $dto->storeId);

        $this->repository->softDelete($user);
    }
}
