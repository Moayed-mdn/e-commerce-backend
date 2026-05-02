<?php

namespace App\Http\Resources\Admin\User;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserDetailResource extends JsonResource
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
            'email'      => $this->email,
            'is_active'  => $this->is_active ?? true,
            'deleted_at' => $this->deleted_at,
            'roles'      => $this->whenLoaded('roles',
                fn() => $this->roles->pluck('name')
            ),
            'created_at' => $this->created_at,
            'phone'      => $this->when(
                $this->phone !== null,
                $this->phone
            ),
            'store_role' => $this->when(
                $this->relationLoaded('stores') || isset($this->pivot),
                fn() => $this->getStoreRole()
            ),
            'orders_count' => $this->when(
                $this->relationLoaded('orders'),
                fn() => $this->orders->count()
            ),
        ];
    }

    private function getStoreRole(): ?string
    {
        if (isset($this->pivot)) {
            return $this->pivot->role ?? null;
        }

        $store = $this->stores?->first();
        if ($store && $store->pivot) {
            return $store->pivot->role ?? null;
        }

        return null;
    }
}
