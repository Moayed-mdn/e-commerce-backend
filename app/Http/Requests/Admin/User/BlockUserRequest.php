<?php

namespace App\Http\Requests\Admin\User;

use App\Enums\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;

class BlockUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(PermissionEnum::USER_BLOCK);
    }

    public function rules(): array
    {
        return [];
    }
}
