<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\ResetPasswordAction;
use App\Actions\Auth\SendResetLinkAction;
use App\DTOs\Auth\ResetPasswordDTO;
use App\DTOs\Auth\SendResetLinkDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Password\ResetPasswordRequest;
use App\Http\Requests\Password\SendResetLinkRequest;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    public function __construct(
        private SendResetLinkAction $sendResetLinkAction,
        private ResetPasswordAction $resetPasswordAction,
    ) {}

    public function sendResetLink(SendResetLinkRequest $request): JsonResponse
    {
        $this->sendResetLinkAction->execute(
            SendResetLinkDTO::fromRequest($request)
        );

        return $this->success(null, __('passwords.sent'));
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $this->resetPasswordAction->execute(
            ResetPasswordDTO::fromRequest($request)
        );

        return $this->success(null, __('passwords.reset'));
    }
}
