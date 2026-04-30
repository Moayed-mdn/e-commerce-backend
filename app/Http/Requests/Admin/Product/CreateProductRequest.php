<?php

namespace App\Http\Requests\Admin\Product;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(RoleEnum::SUPER_ADMIN) 
            || $this->user()->hasPermissionTo('product.create', $this->route('store'));
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'is_active' => ['nullable', 'boolean'],
            
            // Translations (at least one required)
            'translations' => ['required', 'array', 'min:1'],
            'translations.*.locale' => ['required', 'string', 'size:2'],
            'translations.*.name' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.slug' => ['required', 'string', 'max:255'],
            
            // Variants (at least one required)
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.sku' => ['required', 'string', 'max:100'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.quantity' => ['required', 'integer', 'min:0'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'variants.*.manufacture_date' => ['nullable', 'date'],
            'variants.*.expiry_date' => ['nullable', 'date', 'after_or_equal:manufacture_date'],
            'variants.*.batch_number' => ['nullable', 'string', 'max:100'],
            
            // Variant attributes
            'variants.*.attributes' => ['nullable', 'array'],
            'variants.*.attributes.*.attribute_id' => ['required_with:variants.*.attributes', 'exists:attributes,id'],
            'variants.*.attributes.*.attribute_value_id' => ['required_with:variants.*.attributes', 'exists:attribute_values,id'],
            
            // Tags
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
        ];
    }
}
