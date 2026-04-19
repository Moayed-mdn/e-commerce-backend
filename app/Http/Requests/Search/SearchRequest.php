<?php

declare(strict_types=1);

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:products,categories,all'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'query.required' => 'Search query is required.',
            'query.max' => 'Search query must not exceed 255 characters.',
            'type.in' => 'Search type must be one of: products, categories, all.',
            'limit.min' => 'Limit must be at least 1.',
            'limit.max' => 'Limit must not exceed 100.',
            'page.min' => 'Page must be at least 1.',
        ];
    }
}
