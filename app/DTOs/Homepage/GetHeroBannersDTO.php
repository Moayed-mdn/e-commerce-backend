<?php

declare(strict_types=1);

namespace App\DTOs\Homepage;

use App\Http\Requests\HomePage\GetHeroBannersRequest;

class GetHeroBannersDTO
{
    public function __construct() {}

    public static function fromRequest(GetHeroBannersRequest $request): self
    {
        return new self();
    }
}
