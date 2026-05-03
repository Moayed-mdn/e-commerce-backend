<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\Auth\MeRequest;
use App\Models\User;

class GetMeDTO
{
    public function __construct(
        public ?User $user,
    ) {}

    public static function fromRequest(MeRequest $request): self
    {
        return new self($request->user());
    }
}
