<?php

namespace App\Actions\Store;

use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Models\User;

class ValidateStoreMembershipAction
{
    public function execute(User $user, int $storeId): void
    {
        // super_admin bypasses store membership check
        if ($user->hasRole('super_admin')) {
            return;
        }

        $isMember = $user->stores()
            ->where('store_id', $storeId)
            ->exists();

        if (!$isMember) {
            throw new UnauthorizedStoreAccessException();
        }
    }
}
