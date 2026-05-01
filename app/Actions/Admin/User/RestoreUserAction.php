<?php

namespace App\Actions\Admin\User;

use App\DTOs\Admin\User\RestoreUserDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Models\User;
use App\Repositories\Admin\User\AdminUserRepository;

class RestoreUserAction
{
    public function __construct(
        private AdminUserRepository $repository,
    ) {}

    public function execute(RestoreUserDTO $dto): User
    {
        $authUser = auth()->user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        return $this->repository->restore($dto->userId, $dto->storeId);
    }
}
