<?php

declare(strict_types=1);

namespace App\Actions\Product;

use App\DTOs\Product\GetBestSellersDTO;
use App\Services\BestSellerService;

class GetBestSellersAction
{
    public function __construct(
        private BestSellerService $bestSellerService
    ) {}

    public function execute(GetBestSellersDTO $dto): array
    {
        return $this->bestSellerService->getCachedAllParents($dto->storeId, $dto->limit);
    }
}
