<?php

namespace App\Actions\Admin\User;

use App\DTOs\Admin\User\UnblockUserDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Models\User;
use App\Repositories\Admin\User\AdminUserRepository;

class UnblockUserAction
{
    public function __construct(
        private AdminUserRepository $repository,
    ) {}

    public function execute(UnblockUserDTO $dto): User
    {
        $authUser = auth()->user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        $user = $this->repository->findInStore($dto->userId, $dto->storeId);

        return $this->repository->unblock($user);
    }
}
