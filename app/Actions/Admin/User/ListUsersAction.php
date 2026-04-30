<?php

namespace App\Actions\Admin\User;

use App\DTOs\Admin\User\ListUsersDTO;
use App\Repositories\Admin\User\AdminUserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListUsersAction
{
    public function __construct(
        private AdminUserRepository $repository,
    ) {}

    public function execute(ListUsersDTO $dto): LengthAwarePaginator
    {
        return $this->repository->listForStore(
            storeId: $dto->storeId,
            search: $dto->search,
            status: $dto->status,
            perPage: $dto->perPage,
        );
    }
}
