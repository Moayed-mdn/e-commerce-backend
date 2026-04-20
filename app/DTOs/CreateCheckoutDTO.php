<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\Checkout\CreateCheckoutRequest;
use App\Models\User;

class CreateCheckoutDTO
{
    public function __construct(
        public ?User $user,
        public array $items = [],
        public ?string $email = null,
    ) {}

    public static function fromRequest(CreateCheckoutRequest $request): self
    {
        return new self(
            $request->user(),
            $request->input('items', []),
            $request->input('email'),
        );
    }
}
