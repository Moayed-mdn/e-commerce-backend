<?php

namespace App\Http\Resources\Admin\Dashboard;

use App\Enums\Product\ProductStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'status'     => $this->is_active ? ProductStatusEnum::ACTIVE->value : ProductStatusEnum::DRAFT->value,
            'total_sold' => (int)   $this->total_sold,
            'revenue'    => (float) $this->revenue,
            'currency'   => 'usd',
        ];
    }
}
