<?php

namespace App\Actions\Admin\Product;

use App\DTOs\Admin\Product\CreateProductDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Models\Product;
use App\Repositories\Admin\Product\AdminProductRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateProductAction
{
    public function __construct(
        private AdminProductRepository $repository,
    ) {}

    public function execute(CreateProductDTO $dto): Product
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            if (!$authUser->stores()->where('store_id', $dto->storeId)->exists()) {
                throw new UnauthorizedStoreAccessException();
            }
        }

        return DB::transaction(function () use ($dto) {
            $product = $this->repository->create([
                'store_id' => $dto->storeId,
                'category_id' => $dto->categoryId,
                'brand_id' => $dto->brandId,
                'is_active' => $dto->isActive,
            ]);

            foreach ($dto->translations as $translation) {
                $this->repository->createTranslation($product, $translation);
            }

            foreach ($dto->variants as $variantData) {
                $variant = $product->variants()->create([
                    'sku' => $variantData['sku'],
                    'price' => $variantData['price'],
                    'quantity' => $variantData['quantity'],
                    'is_active' => $variantData['is_active'] ?? true,
                    'manufacture_date' => $variantData['manufacture_date'] ?? null,
                    'expiry_date' => $variantData['expiry_date'] ?? null,
                    'batch_number' => $variantData['batch_number'] ?? null,
                ]);

                if (!empty($variantData['attributes'])) {
                    $this->repository->syncVariantAttributes(
                        $variant,
                        $variantData['attributes']
                    );
                }
            }

            if (!empty($dto->tags)) {
                $product->tags()->sync($dto->tags);
            }

            // Set first variant as default for the product
            $firstVariant = $product->variants->first();
            if ($firstVariant) {
                $product->update(['product_variant_id' => $firstVariant->id]);
            }

            return $this->repository->refreshEditorProduct($product);
        });
    }
}
