<?php

namespace App\Actions\Admin\User;

use App\DTOs\Admin\User\ListUsersDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Repositories\Admin\User\AdminUserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ListUsersAction
{
    public function __construct(
        private AdminUserRepository $repository,
    ) {}

    public function execute(ListUsersDTO $dto): LengthAwarePaginator
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        return $this->repository->listForStore(
            storeId: $dto->storeId,
            search: $dto->search,
            status: $dto->status,
            perPage: $dto->perPage,
        );
    }
}
