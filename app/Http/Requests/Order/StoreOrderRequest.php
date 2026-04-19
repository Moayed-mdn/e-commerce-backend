<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address_id' => ['required', 'exists:addresses,id'],
            'billing_address_id' => ['required', 'exists:addresses,id'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'shipping_method' => ['required', 'string', 'max:255'],
        ];
    }
}
