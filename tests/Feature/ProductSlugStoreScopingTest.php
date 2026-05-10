<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSlugStoreScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_by_slug_does_not_leak_products_across_stores_on_fallback_match(): void
    {
        $owner = User::factory()->create();

        $storeA = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Store A',
            'slug' => 'store-a',
            'is_active' => true,
        ]);

        $storeB = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Store B',
            'slug' => 'store-b',
            'is_active' => true,
        ]);

        $category = Category::create([
            'slug' => 'cat',
            'parent_id' => null,
        ]);

        $productA = Product::create([
            'store_id' => $storeA->id,
            'category_id' => $category->id,
            'brand_id' => null,
            'product_variant_id' => null,
            'is_active' => true,
        ]);

        $productB = Product::create([
            'store_id' => $storeB->id,
            'category_id' => $category->id,
            'brand_id' => null,
            'product_variant_id' => null,
            'is_active' => true,
        ]);

        ProductTranslation::create([
            'product_id' => $productA->id,
            'locale' => 'en',
            'name' => 'A',
            'description' => 'A desc',
            'slug' => 'a-slug',
            'seo_title' => null,
            'seo_description' => null,
        ]);

        ProductTranslation::create([
            'product_id' => $productB->id,
            'locale' => 'ar',
            'name' => 'B',
            'description' => 'B desc',
            'slug' => 'shared-slug',
            'seo_title' => null,
            'seo_description' => null,
        ]);

        $result = Product::query()
            ->where('store_id', $storeA->id)
            ->findBySlug('shared-slug', 'en')
            ->first();

        $this->assertNull($result);
    }

    public function test_find_by_slug_returns_product_in_store_when_slug_matches(): void
    {
        $owner = User::factory()->create();

        $store = Store::create([
            'owner_id' => $owner->id,
            'name' => 'Store',
            'slug' => 'store',
            'is_active' => true,
        ]);

        $category = Category::create([
            'slug' => 'cat',
            'parent_id' => null,
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'brand_id' => null,
            'product_variant_id' => null,
            'is_active' => true,
        ]);

        ProductTranslation::create([
            'product_id' => $product->id,
            'locale' => 'en',
            'name' => 'P',
            'description' => 'P desc',
            'slug' => 'p-slug',
            'seo_title' => null,
            'seo_description' => null,
        ]);

        $result = Product::query()
            ->where('store_id', $store->id)
            ->findBySlug('p-slug', 'en')
            ->first();

        $this->assertNotNull($result);
        $this->assertSame($product->id, $result->id);
    }
}
