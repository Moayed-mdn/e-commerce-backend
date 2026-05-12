<?php

namespace App\Actions\Admin\Product;

use App\DTOs\Admin\Product\UpdateProductDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Models\Product;
use App\Repositories\Admin\Product\AdminProductRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateProductAction
{
    public function __construct(
        private AdminProductRepository $repository,
    ) {}

    public function execute(UpdateProductDTO $dto): Product
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        $product = $this->repository->findInStore($dto->productId, $dto->storeId);

        return DB::transaction(function () use ($dto, $product) {
            $this->repository->update($product, array_filter([
                'category_id' => $dto->categoryId,
                'brand_id' => $dto->brandId,
                'is_active' => $dto->isActive,
            ], fn($v) => !is_null($v)));

            if (!is_null($dto->translations)) {
                foreach ($dto->translations as $translation) {
                    $this->repository->upsertTranslation($product, $translation['locale'], $translation);
                }
            }

            if ($dto->syncVariants === true && !is_null($dto->variants)) {

                $existingVariantIds = $product->variants()
                    ->pluck('id')
                    ->toArray();

                $processedVariantIds = [];

                foreach ($dto->variants as $variantData) {

                    $payload = [
                        'sku' => $variantData['sku'],
                        'price' => $variantData['price'],
                        'quantity' => $variantData['quantity'],
                        'is_active' => $variantData['is_active'] ?? true,
                        'manufacture_date' => $variantData['manufacture_date'] ?? null,
                        'expiry_date' => $variantData['expiry_date'] ?? null,
                        'batch_number' => $variantData['batch_number'] ?? null,
                    ];

                    // UPDATE EXISTING VARIANT
                    if (!empty($variantData['id'])) {

                        $variant = $product->variants()
                            ->where('id', $variantData['id'])
                            ->firstOrFail();

                        $this->repository->updateVariant($variant, $payload);
                    }

                    // CREATE NEW VARIANT
                    else {

                        $variant = $this->repository->createVariant(
                            $product,
                            $payload
                        );
                    }

                    $processedVariantIds[] = $variant->id;

                    if (isset($variantData['attributes'])) {
                        $this->repository->syncVariantAttributes(
                            $variant,
                            $variantData['attributes']
                        );
                    }
                }

                // SOFT DELETE REMOVED VARIANTS
                $variantIdsToDelete = array_diff(
                    $existingVariantIds,
                    $processedVariantIds
                );

                if (!empty($variantIdsToDelete)) {

                    $variantsToDelete = $product->variants()
                        ->whereIn('id', $variantIdsToDelete)
                        ->get();

                    foreach ($variantsToDelete as $variant) {

                        $variant->attributeValues()->detach();

                        $this->repository->deleteVariant($variant);
                    }
                }

                // Ensure default variant still exists
                $product->refresh();

                $defaultVariantExists = $product->variants()
                    ->where('id', $product->product_variant_id)
                    ->exists();

                if (!$defaultVariantExists) {

                    $firstVariant = $product->variants()->first();

                    $this->repository->update($product, [
                        'product_variant_id' => $firstVariant?->id,
                    ]);
                }
            }

            if (!is_null($dto->tags)) {
                $this->repository->syncTags($product, $dto->tags);
            }

            return $this->repository->refreshEditorProduct($product);
        });
    }
}
