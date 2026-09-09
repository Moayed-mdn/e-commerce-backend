<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Concerns\SeedsStaticMedia;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\Image;

class ProductVariantSeeder extends Seeder
{
    use SeedsStaticMedia;

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
                $price = fake()->randomFloat(2, 20, 250);
                $costPrice = round($price * 0.6, 2); // Cost is 60% of selling price
                
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => strtoupper(fake()->unique()->bothify('SKU-???-###')),
                    'price' => $price,
                    'cost_price' => $costPrice,
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
                    'image_url' => $this->seedStaticAsset('default-variant.png', 'variants/default.png'),
                    'is_primary' => true
                ]);
            }
        }
    }
}
