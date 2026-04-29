<?php

declare(strict_types=1);

namespace App\Repositories\Category;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    public function getRootCategories(int $storeId, ?string $type = null): Collection
    {
        $query = Category::query()
            ->where('store_id', $storeId)
            ->with(['children', 'parent'])
            ->withCount(['products' => function ($q) use ($storeId) {
                $q->where('status', 'active')
                    ->where('store_id', $storeId);
            }])
            ->whereNull('parent_id');

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public function getChildCategories(int $parentId, int $storeId): Collection
    {
        return Category::query()
            ->where('store_id', $storeId)
            ->where('parent_id', $parentId)
            ->with(['children', 'parent'])
            ->withCount(['products' => function ($q) use ($storeId) {
                $q->where('status', 'active')
                    ->where('store_id', $storeId);
            }])
            ->get();
    }

    public function findById(int $id, int $storeId): ?Category
    {
        return Category::query()
            ->where('store_id', $storeId)
            ->with(['children', 'parent'])
            ->withCount(['products' => function ($q) use ($storeId) {
                $q->where('status', 'active')
                    ->where('store_id', $storeId);
            }])
            ->find($id);
    }

    public function findBySlugOrFail(string $slug, int $storeId): Category
    {
        return Category::where('store_id', $storeId)->findByLocalizedSlugOrFail($slug);
    }

    public function findBySlug(string $slug, int $storeId): ?Category
    {
        return Category::where('store_id', $storeId)->findByLocalizedSlug($slug);
    }

    public function flattenDescendantsWithTranslations(Category $category, string $locale): array
    {
        $result = collect();
        $this->flattenDescendantsRecursive($category, $result, $locale);
        return $result->toArray();
    }

    private function flattenDescendantsRecursive(Category $category, &$result, string $locale): void
    {
        foreach ($category->children as $child) {
            $translation = $child->translation($locale);

            $result->push([
                'id'   => $child->id,
                'name' => $translation?->name ?? $child->slug,
                'slug' => $translation?->slug ?? $child->slug,
            ]);

            if ($child->relationLoaded('descendants') || $child->relationLoaded('children')) {
                $this->flattenDescendantsRecursive($child, $result, $locale);
            }
        }
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
