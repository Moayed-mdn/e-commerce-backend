<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\GetMeDTO;
use App\Enums\RoleEnum;
use App\Models\Store;
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

        $user->load('roles');

        if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            $user->setRelation(
                'stores',
                Store::where('is_active', true)->get()
            );
        } else {
            $user->load('stores');
        }

        return $user;
    }
}
