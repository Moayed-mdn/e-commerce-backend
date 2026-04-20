<?php

declare(strict_types=1);

namespace App\Actions;

use App\DTOs\GetCategoriesDTO;
use App\Services\CategoryService;
use Illuminate\Database\Eloquent\Collection;

class GetCategoriesAction
{
    public function __construct(
        private CategoryService $categoryService,
    ) {}

    public function execute(GetCategoriesDTO $dto): Collection
    {
        if ($dto->parentId !== null && $dto->parentId !== 'null') {
            return $this->categoryService->getChildCategories((int) $dto->parentId);
        }

        return $this->categoryService->getRootCategories($dto->type);
    }
}
