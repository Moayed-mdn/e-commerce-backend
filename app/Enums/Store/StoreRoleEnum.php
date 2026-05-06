<?php

namespace App\Enums\Store;

enum StoreRoleEnum: string
{
    case STORE_ADMIN = 'store_admin';
    case STAFF       = 'staff';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
