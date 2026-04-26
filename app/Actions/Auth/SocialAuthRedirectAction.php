<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\SocialAuthRedirectDTO;
use App\Services\SocialAuthService;
use Illuminate\Http\RedirectResponse;

class SocialAuthRedirectAction
{
    public function __construct(
        private SocialAuthService $socialAuthService
    ) {}

    public function execute(SocialAuthRedirectDTO $dto): RedirectResponse
    {
        // Currently only Google is supported in the service
        return $this->socialAuthService->redirect();
    }
}
