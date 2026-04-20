<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\ResendVerificationEmailDTO;
use App\Exceptions\Auth\TooManyRequestsException;
use App\Exceptions\NotFoundException;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

class ResendVerificationEmailAction
{
    public function execute(ResendVerificationEmailDTO $dto): array
    {
        $user = User::where('email', $dto->email)->first();

        if (!$user) {
            throw new NotFoundException(__('error.user_not_found'));
        }

        if ($user->hasVerifiedEmail()) {
            return ['already_verified' => true];
        }

        $key = 'verification-resend|' . $dto->email . '|' . $dto->ip;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            throw new TooManyRequestsException(trans_choice('auth.too_many_attempts', $seconds, ['seconds' => $seconds]));
        }

        $user->sendEmailVerificationNotification();
        RateLimiter::hit($key, 60);

        return ['already_verified' => false];
    }
}
