<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'avatar'            => $this->getAvatarUrl(),
            'has_password'      => !is_null($this->password),
            'has_google_linked' => !is_null($this->google_id),
            'email_verified_at' => $this->email_verified_at,
            'created_at'        => $this->created_at,
        ];
    }

}
