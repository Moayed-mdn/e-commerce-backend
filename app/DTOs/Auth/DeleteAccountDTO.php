<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\Profile\DeleteAccountRequest;
use App\Models\User;

class DeleteAccountDTO
{
    public function __construct(
        public User $user,
    ) {}

    public static function fromRequest(DeleteAccountRequest $request): self
    {
        return new self($request->user());
    }
}
