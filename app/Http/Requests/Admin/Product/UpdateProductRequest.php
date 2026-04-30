<?php

namespace App\Http\Requests\Admin\Product;

use App\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(RoleEnum::SUPER_ADMIN) 
            || $this->user()->hasPermissionTo('product.update', $this->route('store'));
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'is_active' => ['nullable', 'boolean'],
            
            // Translations
            'translations' => ['nullable', 'array', 'min:1'],
            'translations.*.locale' => ['required_with:translations', 'string', 'size:2'],
            'translations.*.name' => ['required_with:translations', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.slug' => ['required_with:translations', 'string', 'max:255'],
            
            // Variants (optional on update, but if provided must be valid)
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'exists:product_variants,id'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.quantity' => ['nullable', 'integer', 'min:0'],
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
