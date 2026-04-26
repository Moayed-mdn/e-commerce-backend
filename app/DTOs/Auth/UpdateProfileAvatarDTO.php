<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\Http\Requests\Profile\UpdateAvatarRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class UpdateProfileAvatarDTO
{
    public function __construct(
        public User $user,
        public UploadedFile $avatar,
    ) {}

    public static function fromRequest(UpdateAvatarRequest $request): self
    {
        return new self(
            $request->user(),
            $request->file('avatar'),
        );
    }
}
