<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\ChangePasswordAction;
use App\Actions\Auth\GetUserAction;
use App\Actions\Auth\UpdateUserAction;
use App\DTOs\Auth\ChangePasswordDTO;
use App\DTOs\Auth\GetUserDTO;
use App\DTOs\Auth\UpdateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\GetProfileRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private GetUserAction $getUserAction,
        private UpdateUserAction $updateUserAction,
        private ChangePasswordAction $changePasswordAction,
    ) {}

    public function profile(GetProfileRequest $request): JsonResponse
    {
        $user = $this->getUserAction->execute(
            GetUserDTO::fromRequest($request)
        );

        return $this->success(new UserResource($user));
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->updateUserAction->execute(
            UpdateUserDTO::fromRequest($request)
        );

        return $this->success(new UserResource($user));
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->changePasswordAction->execute(
            ChangePasswordDTO::fromRequest($request)
        );

        return $this->success(null, __('auth.password_updated'));
    }
}
