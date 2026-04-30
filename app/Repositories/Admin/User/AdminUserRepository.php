<?php

namespace App\Repositories\Admin\User;

use App\Enums\RoleEnum;
use App\Exceptions\User\UserNotFoundException;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminUserRepository
{
    /**
     * List users belonging to a specific store (via store_user pivot)
     */
    public function listForStore(int $storeId, ?string $search = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query()
            ->join('store_user', 'users.id', '=', 'store_user.user_id')
            ->where('store_user.store_id', $storeId)
            ->select('users.*');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        return $query->paginate($perPage);
    }

    /**
     * Find a user in a specific store or throw exception
     */
    public function findInStore(int $userId, int $storeId): User
    {
        $user = User::query()
            ->join('store_user', 'users.id', '=', 'store_user.user_id')
            ->where('store_user.store_id', $storeId)
            ->where('users.id', $userId)
            ->select('users.*')
            ->first();

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    /**
     * Block a user (set is_active = false)
     */
    public function block(User $user): User
    {
        $user->is_active = false;
        $user->save();

        return $user;
    }

    /**
     * Unblock a user (set is_active = true)
     */
    public function unblock(User $user): User
    {
        $user->is_active = true;
        $user->save();

        return $user;
    }

    /**
     * Soft delete a user
     */
    public function softDelete(User $user): void
    {
        $user->delete();
    }

    /**
     * Restore a trashed user scoped by store
     */
    public function restore(int $userId, int $storeId): User
    {
        $user = User::withTrashed()
            ->join('store_user', 'users.id', '=', 'store_user.user_id')
            ->where('store_user.store_id', $storeId)
            ->where('users.id', $userId)
            ->select('users.*')
            ->first();

        if (!$user) {
            throw new UserNotFoundException();
        }

        $user->restore();

        return $user->fresh();
    }
}
