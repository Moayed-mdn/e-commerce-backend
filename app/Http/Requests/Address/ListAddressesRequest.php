<?php

namespace App\Http\Requests\Address;

use App\Enums\Address\AddressTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListAddressesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', 'string', Rule::in(AddressTypeEnum::values())],
        ];
    }
}
