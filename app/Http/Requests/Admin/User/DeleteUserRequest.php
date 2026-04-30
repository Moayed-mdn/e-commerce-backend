<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\User;

use App\Enums\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;

class DeleteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(PermissionEnum::USER_DELETE);
    }

    public function rules(): array
    {
        return [];
    }
}
