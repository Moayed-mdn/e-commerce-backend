<?php

namespace App\Repositories\Product;

use App\Models\ProductVariant;

class ProductVariantRepository
{
    public function findById(int $id, int $storeId): ProductVariant
    {
        return ProductVariant::whereHas('product', function ($q) use ($storeId) {
            $q->where('store_id', $storeId);
        })->findOrFail($id);
    }

    public function findByIdWithProduct(int $id, int $storeId): ProductVariant
    {
        return ProductVariant::whereHas('product', function ($q) use ($storeId) {
            $q->where('store_id', $storeId);
        })
            ->with(['product.translations'])
            ->findOrFail($id);
    }

    public function findWithLock(int $id, int $storeId): ProductVariant
    {
        return ProductVariant::whereHas('product', function ($q) use ($storeId) {
            $q->where('store_id', $storeId);
        })
            ->lockForUpdate()
            ->findOrFail($id);
    }

    public function incrementStock(ProductVariant $variant, int $quantity): void
    {
        $variant->increment('quantity', $quantity);
    }

    public function decrementStock(ProductVariant $variant, int $quantity): void
    {
        $variant->decrement('quantity', $quantity);
    }
}
