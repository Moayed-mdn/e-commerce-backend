<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateAvatarRequest;
use App\Http\Requests\Profile\UpdateInfoRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    public function show(Request $request): JsonResponse
    {
        return $this->success(new ProfileResource($request->user()));
    }

    public function updateInfo(UpdateInfoRequest $request): JsonResponse
    {
        $user = $this->profileService->updateInfo($request->user(), $request->validated());

        return $this->success(new ProfileResource($user), __('general.profile_updated'));
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $message = $this->profileService->updatePassword($request->user(), $request->validated('password'));

        return $this->success(null, $message);
    }

    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        $avatarUrl = $this->profileService->updateAvatar($request->user(), $request->file('avatar'));

        return $this->success(['avatar' => $avatarUrl], __('general.avatar_updated'));
    }

    public function destroy(Request $request): JsonResponse
    {
        $this->profileService->deleteAccount($request->user());

        return $this->success(null, __('general.account_deleted'));
    }
}