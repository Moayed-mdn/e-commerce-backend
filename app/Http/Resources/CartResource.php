<?php
// app/Http/Resources/CartResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'items' => CartItemResource::collection($this->items),
            'total_items' => $this->items->sum('quantity'),
            'total_price' => $this->items->sum(fn ($item) =>
                $item->quantity * $item->productVariant->price
            ),
        ];
    }
}