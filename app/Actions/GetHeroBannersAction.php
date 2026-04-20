<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\GetHeroBannersDTO;
use App\Services\HomePageService;
use Illuminate\Database\Eloquent\Collection;

class GetHeroBannersAction
{
    public function __construct(
        private HomePageService $homePageService
    ) {}

    public function execute(GetHeroBannersDTO $dto): Collection
    {
        return $this->homePageService->hero();
    }
}
