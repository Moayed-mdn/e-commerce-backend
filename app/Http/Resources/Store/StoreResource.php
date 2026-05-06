<?php

namespace App\Http\Resources\Store;

use App\Enums\User\UserStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'slug'       => $this->slug,
            'domain'     => $this->domain,
            'currency'   => $this->currency ?? 'USD',
            'timezone'   => $this->timezone ?? 'UTC',
            'status'     => $this->is_active ? UserStatusEnum::ACTIVE->value : UserStatusEnum::INACTIVE->value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
