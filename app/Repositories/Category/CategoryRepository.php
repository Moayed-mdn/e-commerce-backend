<?php

declare(strict_types=1);

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function getRootCategories(?string $type = null): Collection
    {
        $query = Category::query()
            ->with(['children', 'parent'])
            ->withCount(['products' => function ($q) {
                $q->where('status', 'active');
            }])
            ->whereNull('parent_id');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public function getChildCategories(int $parentId): Collection
    {
        return Category::query()
            ->where('parent_id', $parentId)
            ->with(['children', 'parent'])
            ->withCount(['products' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get();
    }

    public function findById(int $id): ?Category
    {
        return Category::query()
            ->with(['children', 'parent'])
            ->withCount(['products' => function ($q) {
                $q->where('status', 'active');
            }])
            ->find($id);
    }

    public function getBreadcrumb(Category $category): array
    {
        $breadcrumb = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumb, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
            ]);
            $current = $current->parent;
        }

        return $breadcrumb;
    }
}
