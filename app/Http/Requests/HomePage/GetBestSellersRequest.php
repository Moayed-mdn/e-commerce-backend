<?php

declare(strict_types=1);

namespace App\Http\Requests\HomePage;

use Illuminate\Foundation\Http\FormRequest;

class GetBestSellersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
