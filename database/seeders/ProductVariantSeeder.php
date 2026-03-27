<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\Image;
use Illuminate\Support\Arr;

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\Image;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    public function run()
    {
        $attributeSets = [
            ['Color' => 'Red', 'Size' => '42'],
            ['Color' => 'Blue', 'Size' => '43'],
            ['Color' => 'Black', 'Size' => '44'],
            ['Color' => 'White', 'Size' => '40'],
            ['Material' => 'Leather', 'Size' => 'M'],
            ['Material' => 'Synthetic', 'Size' => 'L'],
            ['Weight' => '1kg', 'Volume' => '500ml'],
        ];

        foreach (Product::all() as $product) {
            for ($i = 0; $i < 3; $i++) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => strtoupper(fake()->unique()->bothify('SKU-???-###')),
                    'price' => fake()->randomFloat(2, 20, 250),
                    'quantity' => fake()->numberBetween(5, 100),
                    'batch_number' => strtoupper(fake()->bothify('BATCH-###')),
                    'manufacture_date' => fake()->dateTimeBetween('-2 years', 'now'),
                    'expiry_date' => fake()->dateTimeBetween('now', '+3 years'),
                    'is_active' => true,
                ]);

                // attributes
                $attributes = collect($attributeSets)->random();
                foreach ($attributes as $name => $value) {
                    Attribute::create([
                        'variant_id' => $variant->id,
                        'attribute_name' => $name,
                        'attribute_value' => $value,
                    ]);
                }

                // image
                Image::create([
                    'imageable_id' => $variant->id,
                    'imageable_type' => ProductVariant::class,
                    'image_url' => '/storage/variants/default.png',
                    'is_primary' => true
                ]);
            }
        }
    }
}
