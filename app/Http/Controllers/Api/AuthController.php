<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Auth\EmailVerificationException;
use App\Exceptions\Auth\InvalidCredentialsException;
use App\Exceptions\Auth\TooManyRequestsException;
use App\Exceptions\Auth\UnauthorizedException;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegistgerUserRequest;
use App\Http\Requests\Auth\ResendVerificationEmailRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function register(RegistgerUserRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $token = $user->createToken('auth-token')->plainTextToken;

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token
        ], __('auth.register_success'), 201);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        if (!$user->hasVerifiedEmail()) {
            throw new UnauthorizedException(__('auth.verify_email_before_login'));
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token
        ], __('auth.login_successful'));
    }


    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, __('auth.logout_successful'));
    }

    public function verifyEmail(Request $request): JsonResponse
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
            return ApiResponse::success(null, __('auth.already_verified'));
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return ApiResponse::success(null, __('auth.email_verified'));
    }

    public function resendVerificationEmail(ResendVerificationEmailRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw new NotFoundException(__('error.user_not_found'));
        }

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, __('auth.email_already_verified'));
        }

        $key = 'verification-resend|' . $user->email . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            throw new TooManyRequestsException(trans_choice('auth.too_many_attempts', $seconds, ['seconds' => $seconds]));
        }

        $user->sendEmailVerificationNotification();
        RateLimiter::hit($key, 60);

        return ApiResponse::success(null, __('auth.verification_email_sent'));
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(new UserResource($request->user()));
    }
}