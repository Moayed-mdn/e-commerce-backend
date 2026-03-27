<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistgerUserRequest;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Requests\Auth\ResendVerificationEmailRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(RegistgerUserRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                    ],
                    'token' => $token
                ],
                'message' => 'User registered successfully. Please check your email for verification.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Registration failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'error' => 'Invalid credentials'
                ], 401);
            }

            // Check if email is verified (optional - remove if you don't require email verification)
            if (!$user->hasVerifiedEmail()) {
                return response()->json([
                    'error' => 'Email not verified',
                    'message' => 'Please verify your email address before logging in.'
                ], 403);
            }

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'data' => [
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
                    'token' => $token
                ],
                'message' => 'Login successful'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Login failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Logout failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyEmail(Request $request)
    {
        try {

            // if (! $request->hasValidSignature()) {
            //     return response()->json([
            //         'message' => 'رابط التفعيل غير صالح أو انتهت صلاحيته.'
            //     ], 403);
            // }
        
          
    
            // 2. Find user and validate
            $user = User::find($request->route('id')); // Use route() instead of direct input
    
            if (!$user) {
                Log::warning('Verification attempt for non-existent user', [
                    'user_id' => $request->route('id'),
                    'ip' => $request->ip()
                ]);
    
                return response()->json([
                    'error' => 'user_not_found',
                    'message' => 'User account not found.'
                ], 404);
            }
    
            // 3. Verify hash matches (additional security layer)
            $expectedHash = sha1($user->getEmailForVerification());
            if (!hash_equals($expectedHash, $request->route('hash'))) {
                Log::warning('Hash mismatch in email verification', [
                    'user_id' => $user->id,
                    'ip' => $request->ip(),
                    'expected_hash' => $expectedHash,
                    'provided_hash' => $request->route('hash')
                ]);
    
                return response()->json([
                    'error' => 'invalid_verification_link',
                    'message' => 'This verification link is invalid.'
                ], 403);
            }
    
            // 4. Check if already verified
            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'This account has already been verified'], 200);
            }
    
            // 5. Mark as verified and trigger events
            $user->markEmailAsVerified();
            event(new Verified($user));
    
            // 6. Log successful verification
            Log::info('Email verified successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'verified_at' => now()->toISOString()
            ]);
    
            // 7. Return success response
            // return response()->json([
            //     'status' => 'verified',
            //     'message' => 'Email verified successfully!',
            //     'data' => [
            //         'user_id' => $user->id,
            //         'email' => $user->email,
            //         'verified_at' => $user->email_verified_at->toISOString()
            //     ]
            // ], 200);

            return response()->json([
                'status' => 'success',
                'message' => 'Email verified successfully!'
            ], 200);
    
        } catch (\Exception $e) {
            Log::error('Email verification error', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip()
            ]);
    
            return response()->json([
                'error' => 'verification_failed',
                'message' => 'An unexpected error occurred during verification.'
            ], 500);
        }
    }

    public function resendVerificationEmail(ResendVerificationEmailRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified'
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent successfully'
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
    
            return response()->json([
                'data' => [
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
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to fetch user',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}