<?php

namespace App\Enums\Address;

enum AddressTypeEnum: string
{
    case SHIPPING = 'shipping';
    case BILLING  = 'billing';
    case BOTH     = 'both';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
