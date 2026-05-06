<?php

namespace App\Enums\Order;

enum OrderStatusEnum: string
{
    case PENDING    = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED    = 'shipped';
    case DELIVERED  = 'delivered';
    case CANCELLED  = 'cancelled';
    case REFUNDED   = 'refunded';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
