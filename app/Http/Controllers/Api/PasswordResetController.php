<?php
// app/Http/Controllers/Api/PasswordResetController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Password\ResetPasswordRequest;
use App\Http\Requests\Password\SendResetLinkRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function sendResetLink(SendResetLinkRequest $request): JsonResponse
    {
        $this->authService->sendResetLink($request->validated());

        return $this->success(null, __('passwords.sent'));
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $this->authService->resetPassword($request->validated());

        return $this->success(null, __('passwords.reset'));
    }
}