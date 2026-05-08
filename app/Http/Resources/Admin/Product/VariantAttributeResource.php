<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class VariantAttributeResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = app()->getLocale();

        $attribute = $this->attribute;
        $attrTranslation = $attribute?->translation($locale);
        $valueTranslation = $this->translation($locale);

        return [
            'attribute_id'        => $attribute?->id,
            'attribute_value_id'  => $this->id,
            'code'                => $attribute?->code ?? '',
            'name'                => $attrTranslation?->name ?? $attribute?->code ?? '',
            'value'               => $valueTranslation?->label ?? $this->code,
            'raw_value'           => $this->code,
        ];
    }
}
