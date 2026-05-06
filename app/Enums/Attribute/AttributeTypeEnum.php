<?php

namespace App\Enums\Attribute;

enum AttributeTypeEnum: string
{
    case TEXT   = 'text';
    case COLOR  = 'color';
    case SIZE   = 'size';
    case NUMBER = 'number';
    case SELECT = 'select';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
