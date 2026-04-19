<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateAvatarRequest;
use App\Http\Requests\Profile\UpdateInfoRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Resources\ProfileResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return ApiResponse::success(new ProfileResource($request->user()));
    }

    public function updateInfo(UpdateInfoRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($user->email !== $validated['email']) {
            $validated['email_verified_at'] = null;
        }

        $user->update($validated);

        return ApiResponse::success(new ProfileResource($user), __('general.profile_updated'));
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $hasPassword = !is_null($user->password);

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        $message = $hasPassword
            ? __('auth.password_updated')
            : __('auth.password_set');

        return ApiResponse::success(null, $message);
    }

    public function updateAvatar(UpdateAvatarRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return ApiResponse::success(['avatar' => Storage::disk('public')->url($path)], __('general.avatar_updated'));
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();

        $user->delete();

        return ApiResponse::success(null, __('general.account_deleted'));
    }
}