<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'imageable_id'=>$this->faker->randomElement(Product::pluck('id')->toArray()), 
            'imageable_type'=>Product::class,
            'alt_text'=>'default image',
            'image_url'=>'/storage/products/default.png'
        ];
    }
}
