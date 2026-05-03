<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\GetMeDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GetMeAction
{
    public function execute(GetMeDTO $dto): User
    {
        /** @var User|null $user */
        $user = $dto->user ?? Auth::user();

        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        return $user->load(['stores', 'roles']);
    }
}
