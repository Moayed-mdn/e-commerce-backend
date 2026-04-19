<?php

namespace App\Http\Requests\PaymentMethod;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'max:255'],
            'payment_method_id' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'last_four' => ['required', 'string', 'size:4'],
            'exp_month' => ['required', 'integer', 'min:1', 'max:12'],
            'exp_year' => ['required', 'integer', 'min:' . date('Y')],
            'is_default' => ['boolean'],
        ];
    }
}
