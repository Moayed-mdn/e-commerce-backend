<?php

namespace App\Http\Requests\Admin\User;

use App\Enums\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;

class GetUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(PermissionEnum::USER_VIEW);
    }

    public function rules(): array
    {
        return [];
    }
}
