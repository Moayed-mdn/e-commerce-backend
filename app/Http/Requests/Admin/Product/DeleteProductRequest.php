<?php

namespace App\Http\Requests\Admin\Product;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;

class DeleteProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(RoleEnum::SUPER_ADMIN) 
            || $this->user()->hasPermissionTo('product.delete', $this->route('store'));
    }

    public function rules(): array
    {
        return [];
    }
}
