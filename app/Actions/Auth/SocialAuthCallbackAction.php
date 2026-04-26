<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\SocialAuthCallbackDTO;
use App\Services\SocialAuthService;
use Illuminate\Http\RedirectResponse;

class SocialAuthCallbackAction
{
    public function __construct(
        private SocialAuthService $socialAuthService
    ) {}

    public function execute(SocialAuthCallbackDTO $dto): RedirectResponse
    {
        // Currently only Google is supported in the service
        return $this->socialAuthService->callback();
    }
}
