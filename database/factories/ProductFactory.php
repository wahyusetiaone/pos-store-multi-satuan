<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Keyboard', 'Mouse', 'Monitor', 'Printer', 'Speaker', 'Webcam', 'Headset', 'Motherboard', 'RAM', 'Hard Drive']),
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'price' => $this->faker->randomFloat(2, 1000, 100000),
            'stock' => $this->faker->numberBetween(0, 100),
            'category_id' => 1, // Should be set dynamically in seeder
            'description' => $this->faker->optional()->sentence(),
            'status' => $this->faker->randomElement([true, false]),
        ];
    }
}



