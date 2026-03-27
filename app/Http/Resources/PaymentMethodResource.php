<?php
// app/Http/Resources/PaymentMethodResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'provider' => $this->provider,
            'brand' => $this->brand,
            'last_four' => $this->last_four,
            'card_number' => $this->card_number,
            'exp_month' => $this->exp_month,
            'exp_year' => $this->exp_year,
            'expiration' => $this->expiration,
            'is_default' => $this->is_default,
            'is_expired' => $this->isExpired(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}