<?php
// app/Http/Resources/ProductVariantResource.php
namespace App\Http\Resources;

use App\Models\ProductVariant;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray($request)
    {
        // return [
        //     'id' => $this->id,
        //     'product_id' => $this->product_id,
        //     'sku' => $this->sku,
        //     'price' => $this->price,
        //     'quantity' => $this->quantity,
        //     'manufacture_date' => $this->manufacture_date,
        //     'expiry_date' => $this->expiry_date,
        //     'batch_number' => $this->batch_number,
        //     'is_active' => $this->is_active,
        //     'attributes' => AttributeResource::collection($this->whenLoaded('attributes')),
        //     'primary_image' => new ImageResource($this->primaryImage),
        //     'images' => ImageResource::collection($this->whenLoaded('images')),
        //     'product' => new ProductResource($this->whenLoaded('product')),
        //     'created_at' => $this->created_at,
        //     'updated_at' => $this->updated_at,
        // ];

        return $this->getProductVariants($this->product_id);
    }


    // ProductController - Get available variants grouped by attributes
public function getProductVariants($productId)
{
    $variants = ProductVariant::with('attributes')
        ->where('product_id', $productId)
        ->where('is_active', true)
        ->get();

    // Group by attributes for display, but keep individual variants
    $grouped = $variants->groupBy(function($variant) {
        return $variant->attributes->sortBy('attribute_name')
                    ->pluck('attribute_value')
                    ->join('|');
    });

    $result = $grouped->map(function($variantGroup, $key) {
        $firstVariant = $variantGroup->first();
        
        return [
            'attribute_display' => $this->getAttributeDisplay($firstVariant),
            'total_quantity' => $variantGroup->sum('quantity'),
            'price_range' => [
                'min' => $variantGroup->min('price'),
                'max' => $variantGroup->max('price')
            ],
            'variants' => $variantGroup->map(function($v) {
                return [
                    'id' => $v->id,
                    'sku' => $v->sku,
                    'price' => $v->price,
                    'quantity' => $v->quantity,
                    'batch_number' => $v->batch_number,
                    'manufacture_date' => $v->manufacture_date,
                    'expiry_date' => $v->expiry_date
                ];
            })
        ];
    });

    return $result->values();
}

private function getAttributeDisplay($variant)
{
    if ($variant->attributes->isEmpty()) {
        return 'Standard'; // Or 'Base Model'
    }
    
    return $variant->attributes->sortBy('attribute_name')
                ->pluck('attribute_value')
                ->join(', ');
}
}