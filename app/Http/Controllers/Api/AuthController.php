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
        ], 'User registered successfully. Please check your email for verification.', 201);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        if (!$user->hasVerifiedEmail()) {
            throw new UnauthorizedException('Please verify your email address before logging in.');
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return ApiResponse::success([
            'user' => new UserResource($user),
            'token' => $token
        ], 'Login successful');
    }


    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out successfully');
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        if (!$request->hasValidSignature()) {
            throw new EmailVerificationException('Invalid or expired verification link.');
        }

        $user = User::findOrFail($request->route('id'));

        $expectedHash = sha1($user->getEmailForVerification());
        if (!hash_equals($expectedHash, $request->route('hash'))) {
            throw new EmailVerificationException('This verification link is invalid.');
        }

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'This account has already been verified');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return ApiResponse::success(null, 'Email verified successfully!');
    }

    public function resendVerificationEmail(ResendVerificationEmailRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw new NotFoundException('User not found');
        }

        if ($user->hasVerifiedEmail()) {
            return ApiResponse::success(null, 'Email already verified');
        }

        $key = 'verification-resend|' . $user->email . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            throw new TooManyRequestsException("You may try again in {$seconds} seconds.");
        }

        $user->sendEmailVerificationNotification();
        RateLimiter::hit($key, 60);

        return ApiResponse::success(null, 'Verification email sent successfully');
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(new UserResource($request->user()));
    }
}