<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\User;

use App\Enums\PermissionEnum;
use Illuminate\Foundation\Http\FormRequest;

class RestoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(PermissionEnum::USER_RESTORE);
    }

    public function rules(): array
    {
        return [];
    }
}
