<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\SocialAuthCallbackAction;
use App\Actions\SocialAuthRedirectAction;
use App\DTOs\SocialAuthCallbackDTO;
use App\DTOs\SocialAuthRedirectDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SocialAuthCallbackRequest;
use App\Http\Requests\Auth\SocialAuthRedirectRequest;
use Illuminate\Http\RedirectResponse;

class SocialAuthController extends Controller
{
    public function __construct(
        private SocialAuthRedirectAction $socialAuthRedirectAction,
        private SocialAuthCallbackAction $socialAuthCallbackAction,
    ) {}

    /**
     * Redirect the user to Google's OAuth page.
     */
    public function redirect(SocialAuthRedirectRequest $request): RedirectResponse
    {
        return $this->socialAuthRedirectAction->execute(
            SocialAuthRedirectDTO::fromRequest($request)
        );
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(SocialAuthCallbackRequest $request): RedirectResponse
    {
        return $this->socialAuthCallbackAction->execute(
            SocialAuthCallbackDTO::fromRequest($request)
        );
    }
}
