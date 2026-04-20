<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\GetBestSellersDTO;
use App\Services\BestSellerService;

class GetBestSellersAction
{
    public function __construct(
        private BestSellerService $bestSellerService
    ) {}

    public function execute(GetBestSellersDTO $dto): array
    {
        return $this->bestSellerService->getCachedAllParents($dto->limit);
    }
}
