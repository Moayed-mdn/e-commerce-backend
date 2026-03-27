<?php
// app/Http/Requests/Order/FilterOrdersRequest.php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class FilterOrdersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'    => ['nullable', 'string', 'in:pending,processing,shipped,delivered,cancelled'],
            'from_date' => ['nullable', 'date'],
            'to_date'   => ['nullable', 'date', 'after_or_equal:from_date'],
            'per_page'  => ['nullable', 'integer', 'min:1', 'max:50'],
            'page'      => ['nullable', 'integer', 'min:1'],
        ];
    }
}