<?php

namespace App\Repositories;

use App\Models\ProductVariant;

class ProductVariantRepository
{
    public function findById(int $id): ProductVariant
    {
        return ProductVariant::findOrFail($id);
    }

    public function findByIdWithProduct(int $id): ProductVariant
    {
        return ProductVariant::with(['product.translations'])->findOrFail($id);
    }

    public function findWithLock(int $id): ProductVariant
    {
        return ProductVariant::lockForUpdate()->findOrFail($id);
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
