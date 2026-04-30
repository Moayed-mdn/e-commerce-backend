<?php

namespace App\Repositories\Admin\Product;

use App\Exceptions\Product\ProductNotFoundException;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class AdminProductRepository
{
    /**
     * List products for a specific store with pagination
     */
    public function listForStore(int $storeId, ?string $search = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query()
            ->where('store_id', $storeId);

        if ($search) {
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        return $query->with(['category', 'defaultVariant', 'activeVariants'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find a product in a specific store or throw exception
     */
    public function findInStore(int $productId, int $storeId): Product
    {
        $product = Product::query()
            ->where('store_id', $storeId)
            ->where('id', $productId)
            ->first();

        if (!$product) {
            throw new ProductNotFoundException();
        }

        return $product;
    }

    /**
     * Find a trashed product in a specific store
     */
    public function findTrashedInStore(int $productId, int $storeId): ?Product
    {
        return Product::withTrashed()
            ->where('store_id', $storeId)
            ->where('id', $productId)
            ->first();
    }

    /**
     * Create a new product
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Update a product
     */
    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    /**
     * Soft delete a product
     */
    public function softDelete(Product $product): void
    {
        $product->delete();
    }

    /**
     * Restore a trashed product
     */
    public function restore(Product $product): Product
    {
        $product->restore();
        return $product->fresh();
    }

    /**
     * Create product translation
     */
    public function createTranslation(int $productId, array $translationData): void
    {
        $product = Product::findOrFail($productId);
        $product->translations()->create($translationData);
    }

    /**
     * Update or create product translation
     */
    public function upsertTranslation(int $productId, string $locale, array $translationData): void
    {
        $product = Product::findOrFail($productId);
        $product->translations()->updateOrCreate(
            ['locale' => $locale],
            $translationData
        );
    }

    /**
     * Delete all translations for a product
     */
    public function deleteTranslations(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $product->translations()->delete();
    }
}
