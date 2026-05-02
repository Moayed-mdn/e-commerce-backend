<?php

namespace App\Http\Resources\Admin\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreStatsResource extends JsonResource
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
            'total_revenue'      => (float) ($this['total_revenue'] ?? 0),
            'total_orders'       => (int)   ($this['total_orders'] ?? 0),
            'total_customers'    => (int)   ($this['total_customers'] ?? 0),
            'total_products'     => (int)   ($this['total_products'] ?? 0),
            'revenue_change'     => (float) ($this['revenue_change'] ?? 0),
            'orders_change'      => (float) ($this['orders_change'] ?? 0),
            'customers_change'   => (float) ($this['customers_change'] ?? 0),
            'products_change'    => (float) ($this['products_change'] ?? 0),
        ];
    }
}
