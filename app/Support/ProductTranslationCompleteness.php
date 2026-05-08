<?php

namespace App\Support;

use App\Models\ProductTranslation;

class ProductTranslationCompleteness
{
    /**
     * Fields required for an admin editor locale to be considered complete.
     *
     * @return list<string>
     */
    public static function requiredFields(): array
    {
        return [
            'name',
            'slug',
            'description',
            'seo_title',
            'seo_description',
        ];
    }

    public static function isComplete(ProductTranslation|array|null $translation): bool
    {
        if ($translation === null) {
            return false;
        }

        foreach (self::requiredFields() as $field) {
            $value = data_get($translation, $field);

            if (!is_string($value) || trim($value) === '') {
                return false;
            }
        }

        return true;
    }
}
