<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginResultDTO;
use App\DTOs\Auth\LoginUserDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\UnauthorizedException;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginUserAction
{
    public function execute(LoginUserDTO $dto): LoginResultDTO
    {
        $user = User::where('email', $dto->email)->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        if (!$user->hasVerifiedEmail()) {
            throw new UnauthorizedException(__('auth.verify_email_before_login'));
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            $user->setRelation(
                'stores',
                Store::where('is_active', true)->get()
            );
        } else {
            $user->load('stores');
        }

        return new LoginResultDTO($user, $token);
    }
}
