<?php

namespace App\Actions\Admin\Product;

use App\DTOs\Admin\Product\CreateProductDTO;
use App\Enums\RoleEnum;
use App\Exceptions\Store\UnauthorizedStoreAccessException;
use App\Models\Product;
use App\Repositories\Admin\Product\AdminProductRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateProductAction
{
    public function __construct(
        private AdminProductRepository $repository,
    ) {}

    public function execute(CreateProductDTO $dto): Product
    {
        /** @var \App\Models\User $authUser */
        $authUser = Auth::user();
        if (!$authUser->hasRole(RoleEnum::SUPER_ADMIN)) {
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
                $this->repository->createTranslation($product->id, $translation);
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
                    foreach ($variantData['attributes'] as $attr) {
                        $variant->attributeValues()->attach($attr['attribute_value_id']);
                    }
                }
            }

            if (!empty($dto->tags)) {
                $product->tags()->sync($dto->tags);
            }

            return $product->load(['category', 'variants.attributeValues', 'translations']);
        });
    }
}
