<?php

namespace App\Enums\Product;

enum ProductStatusEnum: string
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
    case DRAFT    = 'draft';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
