<?php

declare(strict_types=1);

namespace App\Repositories\Admin\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class AdminUserRepository
{
    public function findByStoreAndId(int $storeId, int $userId): ?User
    {
        return User::whereHas('stores', function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })
            ->where('id', $userId)
            ->withTrashed()
            ->first();
    }

    public function findAllByStore(int $storeId, int $perPage, int $page)
    {
        return User::whereHas('stores', function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })
            ->withTrashed()
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function blockUser(int $storeId, int $userId): bool
    {
        $user = $this->findByStoreAndId($storeId, $userId);
        
        if (!$user) {
            return false;
        }

        $user->update(['is_active' => false]);
        
        return true;
    }

    public function unblockUser(int $storeId, int $userId): bool
    {
        $user = $this->findByStoreAndId($storeId, $userId);
        
        if (!$user) {
            return false;
        }

        $user->update(['is_active' => true]);
        
        return true;
    }

    public function deleteUserFromStore(int $storeId, int $userId): bool
    {
        $user = $this->findByStoreAndId($storeId, $userId);
        
        if (!$user) {
            return false;
        }

        $user->stores()->detach($storeId);
        
        return true;
    }

    public function restoreUserInStore(int $storeId, int $userId): bool
    {
        $user = User::withTrashed()->find($userId);
        
        if (!$user) {
            return false;
        }

        // Check if user was previously associated with this store
        $existingPivot = DB::table('store_user')
            ->where('user_id', $userId)
            ->where('store_id', $storeId)
            ->first();

        if ($existingPivot) {
            // Re-attach with original role
            $user->stores()->attach($storeId, ['role' => $existingPivot->role]);
        } else {
            // Attach as staff by default
            $user->stores()->attach($storeId, ['role' => 'staff']);
        }
        
        return true;
    }
}
