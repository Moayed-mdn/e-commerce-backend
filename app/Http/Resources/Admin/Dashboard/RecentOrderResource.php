<?php

namespace App\Http\Resources\Admin\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecentOrderResource extends JsonResource
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
            'id' => $this->id,
            'status' => $this->status,
            'total' => $this->total,
            'created_at' => $this->created_at,
            'customer_name' => $this->whenLoaded('user', fn () => $this->user->name),
        ];
    }
}
