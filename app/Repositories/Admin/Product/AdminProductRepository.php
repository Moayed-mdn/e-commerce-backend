<?php

namespace App\Repositories\Admin\Product;

use App\Enums\Product\ProductStatusEnum;
use App\Exceptions\Product\ProductNotFoundException;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminProductRepository
{
    /**
     * Relations required by the admin product editor response.
     *
     * @return list<string>
     */
    private function editorRelations(): array
    {
        return [
            'category',
            'variants.attributeValues.attribute.translations',
            'variants.attributeValues.translations',
            'variants.images',
            'translations',
            'tags',
        ];
    }

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

        if ($status === ProductStatusEnum::ACTIVE->value) {
            $query->where('is_active', true);
        } elseif ($status === ProductStatusEnum::INACTIVE->value) {
            $query->where('is_active', false);
        }

        return $query->with(['category', 'variants', 'variants.images', 'translations'])
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
     * Find a product in a specific store with admin editor relations loaded.
     */
    public function findEditorProductInStore(int $productId, int $storeId): Product
    {
        $product = Product::query()
            ->with($this->editorRelations())
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
     * Reload a product with admin editor relations after a write operation.
     */
    public function refreshEditorProduct(Product $product): Product
    {
        return $product->fresh($this->editorRelations()) ?? $product->load($this->editorRelations());
    }

    /**
     * Create product translation
     */
    public function createTranslation(Product $product, array $translationData): void
    {
        $product->translations()->create($translationData);
    }

    /**
     * Update or create product translation
     */
    public function upsertTranslation(Product $product, string $locale, array $translationData): void
    {
        $product->translations()->updateOrCreate(
            ['locale' => $locale],
            $translationData
        );
    }

    /**
     * Delete all translations for a product
     */
    public function deleteTranslations(Product $product): void
    {
        $product->translations()->delete();
    }

    public function deleteAllVariants(Product $product): void
    {
        $variants = $product->variants()->get();

        foreach ($variants as $variant) {
            $variant->attributeValues()->detach();
            $this->deleteVariant($variant);
        }
    }

    public function syncTags(Product $product, array $tags): void
    {
        $product->tags()->sync($tags);
    }

    /**
     * Create a product variant
     */
    public function createVariant(Product $product, array $variantData): ProductVariant
    {
        return $product->variants()->create($variantData);
    }

    /**
     * Update a product variant
     */
    public function updateVariant(ProductVariant $variant, array $data): ProductVariant
    {
        $variant->update($data);
        return $variant->fresh();
    }

    /**
     * Delete a product variant
     */
    public function deleteVariant(ProductVariant $variant): void
    {
        $variant->delete();
    }

    /**
     * Sync variant attributes (pivot table)
     */
    public function syncVariantAttributes(ProductVariant $variant, array $attributes): void
    {
        $variant->attributeValues()->sync(
            collect($attributes)->pluck('attribute_value_id')->toArray()
        );
    }
}
