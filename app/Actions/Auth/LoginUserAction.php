<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginUserDTO;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\UnauthorizedException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginUserAction
{
    public function execute(LoginUserDTO $dto): User
    {
        $user = User::where('email', $dto->email)->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        if (!$user->hasVerifiedEmail()) {
            throw new UnauthorizedException(__('auth.verify_email_before_login'));
        }

        Auth::login($user);

        return $user;
    }
}
