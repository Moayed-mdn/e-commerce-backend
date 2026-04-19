<?php
// app/Http/Requests/Order/GuestOrderLookupRequest.php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class GuestOrderLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_number' => ['required', 'string'],
            'email'        => ['required', 'email'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_number.required' => __('error.order_number_required'),
            'email.required'        => __('error.checkout_email_required'),
            'email.email'           => __('error.email_invalid'),
        ];
    }
}