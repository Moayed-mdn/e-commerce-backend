<?php

namespace App\Enums\HeroBanner;

enum HeroLinkTargetEnum: string
{
    case SELF  = '_self';
    case BLANK = '_blank';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
