<?php
// app/Http/Resources/ProductVariantResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'sku'              => $this->sku,
            'price'            => (float) $this->price,
            'stock'            => $this->quantity,
            'is_active'        => $this->is_active,
            'manufacture_date' => $this->manufacture_date,
            'expiry_date'      => $this->expiry_date,
            'images'           => $this->whenLoaded('images', function () {
                return $this->images->map(fn($img) => [
                    'id'         => $img->id,
                    'url'        => asset($img->image_url),
                    'alt_text'   => $img->alt_text,
                    'is_primary' => $img->is_primary,
                ]);
            }),
            'attributes'       => $this->whenLoaded('attributeValues', function () {
                return $this->attributeValues->map(fn($av) => [
                    'name'  => $av->attribute->code ?? '',
                    'value' => $av->code ?? '',
                ]);
            }),
        ];
    }
}
