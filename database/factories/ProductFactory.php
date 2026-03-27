<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Closure;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(12),
            'category_id' => Category::inRandomOrder()->first()->id,
            'brand_id' => Brand::inRandomOrder()->first()->id,
            'is_active' => true,
        ];
    }


    public function configure()
    {
        return $this->afterCreating(function (Product $product)
        {
            Image::factory()->create([
                'imageable_type' => 'App\Models\Product',
                'imageable_id' => $product->id,
                'image_url' => '/storage/products/default.png',
                'is_primary' => true
            ]);
        });
    }
}
