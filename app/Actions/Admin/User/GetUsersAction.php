<?php

namespace App\Actions\Admin\User;

use App\DTOs\Admin\User\GetUsersDTO;
use App\Models\Store;
use App\Repositories\Admin\User\AdminUserRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class GetUsersAction
{
    public function __construct(
        private AdminUserRepository $repository,
    ) {}

    public function execute(GetUsersDTO $dto): LengthAwarePaginator
    {
        $store = Store::findOrFail($dto->storeId);

        return $this->repository->getStoreUsers($store, $dto->perPage);
    }
}
