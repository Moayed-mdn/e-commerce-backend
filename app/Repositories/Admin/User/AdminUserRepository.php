<?php

namespace App\Repositories\Admin\User;

use App\Models\User;
use App\Models\Store;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminUserRepository
{
    /**
     * Get paginated list of users for a specific store.
     */
    public function getStoreUsers(Store $store, int $perPage = 15): LengthAwarePaginator
    {
        return User::whereHas('stores', function ($query) use ($store) {
                $query->where('store_id', $store->id);
            })
            ->withTrashed()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find a user by ID within a specific store context.
     */
    public function findByStoreAndId(Store $store, int $userId): ?User
    {
        return User::whereHas('stores', function ($query) use ($store) {
                $query->where('store_id', $store->id);
            })
            ->withTrashed()
            ->find($userId);
    }

    /**
     * Block a user in a specific store (set is_active to false).
     */
    public function blockUser(Store $store, User $user): void
    {
        $user->update(['is_active' => false]);
    }

    /**
     * Unblock a user in a specific store (set is_active to true).
     */
    public function unblockUser(Store $store, User $user): void
    {
        $user->update(['is_active' => true]);
    }

    /**
     * Soft delete a user from a store (remove store_user pivot).
     */
    public function deleteUserFromStore(Store $store, User $user): void
    {
        $user->stores()->detach($store->id);
    }

    /**
     * Restore a soft-deleted user to a store.
     */
    public function restoreUserToStore(Store $store, User $user, string $role = 'staff'): void
    {
        $user->restore();
        $user->stores()->attach($store->id, ['role' => $role]);
    }

    /**
     * Check if a user is a member of a store.
     */
    public function isUserMemberOfStore(User $user, int $storeId): bool
    {
        return $user->stores()->where('store_id', $storeId)->exists();
    }
}
