<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\GetMeAction;
use App\Actions\LoginUserAction;
use App\Actions\LogoutUserAction;
use App\Actions\RegisterUserAction;
use App\Actions\ResendVerificationEmailAction;
use App\Actions\VerifyEmailAction;
use App\DTOs\GetMeDTO;
use App\DTOs\LoginUserDTO;
use App\DTOs\LogoutDTO;
use App\DTOs\RegisterUserDTO;
use App\DTOs\ResendVerificationEmailDTO;
use App\DTOs\VerifyEmailDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\MeRequest;
use App\Http\Requests\Auth\RegistgerUserRequest;
use App\Http\Requests\Auth\ResendVerificationEmailRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private RegisterUserAction $registerUserAction,
        private LoginUserAction $loginUserAction,
        private LogoutUserAction $logoutUserAction,
        private VerifyEmailAction $verifyEmailAction,
        private ResendVerificationEmailAction $resendVerificationEmailAction,
        private GetMeAction $getMeAction,
    ) {}

    public function register(RegistgerUserRequest $request): JsonResponse
    {
        $result = $this->registerUserAction->execute(
            RegisterUserDTO::fromRequest($request)
        );

        return $this->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token']
        ], __('auth.register_success'), 201);
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $result = $this->loginUserAction->execute(
            LoginUserDTO::fromRequest($request)
        );

        return $this->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token']
        ], __('auth.login_successful'));
    }

    public function logout(LogoutRequest $request): JsonResponse
    {
        $this->logoutUserAction->execute(
            LogoutDTO::fromRequest($request)
        );

        return $this->success(null, __('auth.logout_successful'));
    }

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $result = $this->verifyEmailAction->execute(
            VerifyEmailDTO::fromRequest($request)
        );

        if ($result['already_verified']) {
            return $this->success(null, __('auth.already_verified'));
        }

        return $this->success(null, __('auth.email_verified'));
    }

    public function resendVerificationEmail(ResendVerificationEmailRequest $request): JsonResponse
    {
        $result = $this->resendVerificationEmailAction->execute(
            ResendVerificationEmailDTO::fromRequest($request)
        );

        if ($result['already_verified']) {
            return $this->success(null, __('auth.email_already_verified'));
        }

        return $this->success(null, __('auth.verification_email_sent'));
    }

    public function me(MeRequest $request): JsonResponse
    {
        $user = $this->getMeAction->execute(
            GetMeDTO::fromRequest($request)
        );

        return $this->success(new UserResource($user));
    }
}
