<?php

declare(strict_types=1);

namespace App\Repositories\Search;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SearchRepository
{
    public function searchProducts(string $query, int $limit, int $page): LengthAwarePaginator
    {
        return Product::query()
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function searchCategories(string $query, int $limit, int $page): LengthAwarePaginator
    {
        return Category::query()
            ->where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function searchAll(string $query, int $limit, int $page): array
    {
        $adjustedLimit = (int) ceil($limit / 2);

        $products = Product::query()
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->limit($adjustedLimit)
            ->get();

        $categories = Category::query()
            ->where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->limit($adjustedLimit)
            ->get();

        return [
            'products' => $products,
            'categories' => $categories,
        ];
    }
}
