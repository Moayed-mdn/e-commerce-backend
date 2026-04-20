<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\VerifyEmailDTO;
use App\Exceptions\Auth\EmailVerificationException;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

class VerifyEmailAction
{
    public function execute(VerifyEmailDTO $dto): array
    {
        $user = User::findOrFail($dto->id);

        $expectedHash = sha1($user->getEmailForVerification());
        if (!hash_equals($expectedHash, $dto->hash)) {
            throw new EmailVerificationException(__('auth.verification_link_invalid'));
        }

        if ($user->hasVerifiedEmail()) {
            return ['already_verified' => true];
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return ['already_verified' => false];
    }
}
