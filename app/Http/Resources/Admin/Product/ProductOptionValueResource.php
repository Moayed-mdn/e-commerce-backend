<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionValueResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = app()->getLocale();

        return [
            'id'    => $this->id,
            'code'  => $this->code,
            'label' => $this->translation($locale)?->label ?? $this->code,
        ];
    }
}
