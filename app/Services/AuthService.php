<?php

namespace App\Services;

use App\Exceptions\Auth\EmailVerificationException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\TooManyRequestsException;
use App\Exceptions\Auth\UnauthorizedException;
use App\Exceptions\NotFoundException;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthService
{
    /**
     * Register a new user.
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        event(new Registered($user));

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Authenticate a user.
     */
    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new InvalidCredentialsException();
        }

        if (!$user->hasVerifiedEmail()) {
            throw new UnauthorizedException(__('auth.verify_email_before_login'));
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout a user by deleting current token.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    /**
     * Verify user email from signed request.
     */
    public function verifyEmail(Request $request): array
    {
        if (!$request->hasValidSignature()) {
            throw new EmailVerificationException(__('auth.invalid_verification_link'));
        }

        $user = User::findOrFail($request->route('id'));

        $expectedHash = sha1($user->getEmailForVerification());
        if (!hash_equals($expectedHash, $request->route('hash'))) {
            throw new EmailVerificationException(__('auth.verification_link_invalid'));
        }

        if ($user->hasVerifiedEmail()) {
            return ['already_verified' => true];
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return ['already_verified' => false];
    }

    /**
     * Resend verification email with rate limiting.
     */
    public function resendVerificationEmail(string $email, string $ip): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new NotFoundException(__('error.user_not_found'));
        }

        if ($user->hasVerifiedEmail()) {
            return ['already_verified' => true];
        }

        $key = 'verification-resend|' . $email . '|' . $ip;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            throw new TooManyRequestsException(trans_choice('auth.too_many_attempts', $seconds, ['seconds' => $seconds]));
        }

        $user->sendEmailVerificationNotification();
        RateLimiter::hit($key, 60);

        return ['already_verified' => false];
    }
}
