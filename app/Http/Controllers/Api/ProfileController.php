<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'user' => [
                    'id'                => $user->id,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'phone'             => $user->phone,
                    'avatar'            => $user->avatar,
                    'has_password'      => !is_null($user->password),  // Google-only users won't have one
                    'has_google_linked' => !is_null($user->google_id),
                    'email_verified_at' => $user->email_verified_at,
                    'created_at'        => $user->created_at,
                ],
            ],
        ]);
    }

    /**
     * Update profile info (name, email, phone).
     */
    public function updateInfo(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        // If the email changed, reset verification
        if ($user->email !== $validated['email']) {
            $validated['email_verified_at'] = null;
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data'    => [
                'user' => [
                    'id'                => $user->id,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'phone'             => $user->phone,
                    'avatar'            => $user->avatar,
                    'has_password'      => !is_null($user->password),
                    'has_google_linked' => !is_null($user->google_id),
                    'email_verified_at' => $user->email_verified_at,
                    'created_at'        => $user->created_at,
                ],
            ],
        ]);
    }

    /**
     * Update password.
     * - Users WITH a password: must provide current_password
     * - Google-only users (no password): can SET their first password without current_password
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $hasPassword = !is_null($user->password);

        $rules = [
            'password'              => ['required', 'confirmed', Password::min(8)],
        ];

        // Only require current_password if the user already has one
        if ($hasPassword) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $validated = $request->validate($rules);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => $hasPassword
                ? 'Password updated successfully.'
                : 'Password set successfully. You can now log in with email and password.',
        ]);
    }

    /**
     * Update avatar (upload).
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old avatar if it's a local file (not a Google URL)
        if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return response()->json([
            'message' => 'Avatar updated successfully.',
            'data'    => [
                'avatar' => $path,
            ],
        ]);
    }

    /**
     * Delete account.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        // Revoke all tokens
        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully.',
        ]);
    }
}