<?php

namespace App\Enums\Order;

enum PaymentStatusEnum: string
{
    case PENDING           = 'pending';
    case PAID              = 'paid';
    case FAILED            = 'failed';
    case REFUNDED          = 'refunded';
    case PARTIALLY_REFUNDED = 'partially_refunded';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
