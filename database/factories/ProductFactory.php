<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'image' => 'https://loremflickr.com/640/480/food?random=' . rand(1, 1000),
            'status' => $this->faker->randomElement(['disponivel', 'esgotado', 'oculto']),
            'price' => $this->faker->randomFloat(2, 10, 50),
            'description' => $this->faker->paragraph(1),
            'preparation_time' => $this->faker->numberBetween(10, 45),
        ];
    }
}
