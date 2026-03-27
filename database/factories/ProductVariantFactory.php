<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\Product;
use App\Models\ProductVariant;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => $this->faker->randomElement(Product::pluck('id')->toArray()),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-???-###')),
            'price' => fake()->randomFloat(2, 20, 200),
            'quantity' => fake()->numberBetween(10, 100),
            'manufacture_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'expiry_date' => fake()->dateTimeBetween('now', '+3 years'),
            'batch_number' => strtoupper(fake()->bothify('BATCH-###')),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function(ProductVariant $productVariant) 
        {
            Image::factory()->create([
                'imageable_type' => ProductVariant::class,
                'imageable_id' => $productVariant->id,
                'image_url' => '/storage/variants/default.png',
                'is_primary' => true
            ]);
        });
    }
}
