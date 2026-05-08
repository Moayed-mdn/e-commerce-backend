<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionResource extends JsonResource
{
    public function toArray($request): array
    {
        $locale = app()->getLocale();

        return [
            'id'    => $this->id,
            'code'  => $this->code,
            'name'  => $this->translation($locale)?->name ?? $this->code,
            'type'  => $this->type->value,
            'values' => ProductOptionValueResource::collection(
                $this->whenLoaded('values')
            ),
        ];
    }
}
