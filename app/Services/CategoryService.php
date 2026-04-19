<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Repositories\Category\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function __construct(
        private CategoryRepository $repository,
    ) {}

    public function getRootCategories(?string $type = null): Collection
    {
        return $this->repository->getRootCategories($type);
    }

    public function getChildCategories(int $parentId): Collection
    {
        return $this->repository->getChildCategories($parentId);
    }

    public function getCategoryById(int $id): ?Category
    {
        return $this->repository->findById($id);
    }

    public function getBreadcrumb(Category $category): array
    {
        return $this->repository->getBreadcrumb($category);
    }
}
