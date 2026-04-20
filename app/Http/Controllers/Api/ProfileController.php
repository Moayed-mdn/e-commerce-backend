<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\DeleteAccountAction;
use App\Actions\GetProfileAction;
use App\Actions\UpdateProfileAvatarAction;
use App\Actions\UpdateProfileInfoAction;
use App\Actions\UpdateProfilePasswordAction;
use App\DTOs\DeleteAccountDTO;
use App\DTOs\GetProfileDTO;
use App\DTOs\UpdateProfileAvatarDTO;
use App\DTOs\UpdateProfileInfoDTO;
use App\DTOs\UpdateProfilePasswordDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\DeleteAccountRequest;
use App\Http\Requests\Profile\GetProfileRequest;
use App\Http\Requests\Profile\UpdateAvatarRequest;
use App\Http\Requests\Profile\UpdateInfoRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    public function __construct(
        private GetProfileAction $getProfileAction,
        private UpdateProfileInfoAction $updateProfileInfoAction,
        private UpdateProfilePasswordAction $updateProfilePasswordAction,
        private UpdateProfileAvatarAction $updateProfileAvatarAction,
        private DeleteAccountAction $deleteAccountAction,
    ) {}

    public function show(GetProfileRequest $request): JsonResponse
    {
        $user = $this->getProfileAction->execute(
            GetProfileDTO::fromRequest($request)
        );

        return $this->success(new ProfileResource($user));
    }

    public function updateInfo(UpdateInfoRequest $request): JsonResponse
    {
        $user = $this->updateProfileInfoAction->execute(
            UpdateProfileInfoDTO::fromRequest($request)
        );

        return $this->success(new ProfileResource($user), __('general.profile_updated'));
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $this->updateProfilePasswordAction->execute(
            UpdateProfilePasswordDTO::fromRequest($request)
        );

        return $this->success(null, __('general.password_updated'));
    }

    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        $avatarUrl = $this->updateProfileAvatarAction->execute(
            UpdateProfileAvatarDTO::fromRequest($request)
        );

        return $this->success(['avatar' => $avatarUrl], __('general.avatar_updated'));
    }

    public function destroy(DeleteAccountRequest $request): JsonResponse
    {
        $this->deleteAccountAction->execute(
            DeleteAccountDTO::fromRequest($request)
        );

        return $this->success(null, __('general.account_deleted'));
    }
}
