<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class SetDefaultAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
