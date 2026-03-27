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
            'order_number.required' => 'Please enter your order number.',
            'email.required'        => 'Please enter the email used during checkout.',
            'email.email'           => 'Please enter a valid email address.',
        ];
    }
}