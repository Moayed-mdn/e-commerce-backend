<?php

namespace App\Repositories\Admin\User;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminUserRepository
{
    public function getPaginated(
        int $storeId,
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $role = null,
    ): LengthAwarePaginator {
        $query = User::query()
            ->whereHas('stores', fn($q) => $q->where('store_id', $storeId))
            ->withTrashed();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function findByStoreAndId(int $storeId, int $userId): ?User
    {
        return User::whereHas('stores', fn($q) => $q->where('store_id', $storeId))
            ->withTrashed()
            ->findOrFail($userId);
    }

    public function blockUser(int $storeId, int $userId): void
    {
        $user = $this->findByStoreAndId($storeId, $userId);
        $user->update(['is_active' => false]);
    }

    public function unblockUser(int $storeId, int $userId): void
    {
        $user = $this->findByStoreAndId($storeId, $userId);
        $user->update(['is_active' => true]);
    }

    public function deleteUser(int $storeId, int $userId): void
    {
        $user = $this->findByStoreAndId($storeId, $userId);
        
        // Detach user from store (soft delete via pivot removal)
        $user->stores()->detach($storeId);
    }

    public function restoreUser(int $storeId, int $userId): void
    {
        $user = User::withTrashed()->findOrFail($userId);
        
        // Re-attach user to store with default role
        if (!$user->stores()->where('store_id', $storeId)->exists()) {
            $user->stores()->attach($storeId, ['role' => 'staff']);
        }
    }

    public function getUserWithStoreRole(int $storeId, int $userId): ?User
    {
        return User::whereHas('stores', fn($q) => $q->where('store_id', $storeId))
            ->with(['stores' => fn($q) => $q->where('store_id', $storeId)])
            ->withTrashed()
            ->findOrFail($userId);
    }
}
