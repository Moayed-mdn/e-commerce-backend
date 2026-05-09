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

            if (!is_null($dto->variants)) {
                // Sync variants: delete existing and create new ones (full replacement strategy)
                $this->repository->deleteAllVariants($product);

                foreach ($dto->variants as $variantData) {
                    $variant = $this->repository->createVariant($product, [
                        'sku' => $variantData['sku'],
                        'price' => $variantData['price'],
                        'quantity' => $variantData['quantity'],
                        'is_active' => $variantData['is_active'] ?? true,
                        'manufacture_date' => $variantData['manufacture_date'] ?? null,
                        'expiry_date' => $variantData['expiry_date'] ?? null,
                        'batch_number' => $variantData['batch_number'] ?? null,
                    ]);

                    if (!empty($variantData['attributes'])) {
                        $this->repository->syncVariantAttributes($variant, $variantData['attributes']);
                    }
                }

                // Set first variant as default for the product
                $product->refresh();
                $firstVariant = $product->variants()->first();
                if ($firstVariant) {
                    $this->repository->update($product, ['product_variant_id' => $firstVariant->id]);
                }
            }

            if (!is_null($dto->tags)) {
                $this->repository->syncTags($product, $dto->tags);
            }

            return $this->repository->refreshEditorProduct($product);
        });
    }
}
