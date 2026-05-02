<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminProductDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'brand_id'    => $this->brand_id,
            'is_active'   => $this->is_active,
            'variants'    => $this->whenLoaded('variants', fn() =>
                $this->variants->map(fn($v) => [
                    'id'               => $v->id,
                    'sku'              => $v->sku,
                    'price'            => (float) $v->price,
                    'quantity'         => $v->quantity,
                    'is_active'        => $v->is_active,
                    'manufacture_date' => $v->manufacture_date,
                    'expiry_date'      => $v->expiry_date,
                    'images'           => $v->relationLoaded('images')
                        ? $v->images->map(fn($img) => [
                            'id'         => $img->id,
                            'url'        => asset($img->image_url),
                            'alt_text'   => $img->alt_text,
                            'is_primary' => $img->is_primary,
                        ])
                        : [],
                    'attributes'       => $v->relationLoaded('attributeValues')
                        ? $v->attributeValues->map(fn($av) => [
                            'name'  => $av->attribute->code ?? '',
                            'value' => $av->code ?? '',
                        ])
                        : [],
                ])
            ),
            'created_at'  => $this->created_at,
        ];
    }
}
