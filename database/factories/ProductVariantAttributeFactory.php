<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attribute>
 */
class AttributeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attribute_name' => $this->faker->word,
            'attribute_value' => $this->faker->word,
            'variant_id' => $this->faker->randomElement(ProductVariant::pluck('id')->toArray()),
        ];
    }
}
