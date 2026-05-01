<?php

namespace App\Actions\Admin\User;

use App\DTOs\Admin\User\GetUserDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Models\User;
use App\Repositories\Admin\User\AdminUserRepository;

use Illuminate\Support\Facades\Auth;

class GetUserAction
{
    public function __construct(
        private AdminUserRepository $repository,
    ) {}

    public function execute(GetUserDTO $dto): User
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        $user = $this->repository->findInStore($dto->userId, $dto->storeId);

        return $user;
    }
}
