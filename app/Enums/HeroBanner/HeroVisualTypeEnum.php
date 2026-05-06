<?php

namespace App\Enums\HeroBanner;

enum HeroVisualTypeEnum: string
{
    case IMAGE    = 'image';
    case GRADIENT = 'gradient';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
