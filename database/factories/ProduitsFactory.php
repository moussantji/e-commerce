<?php

namespace Database\Factories;

use App\Models\Produits;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProduitsFactory extends Factory
{
    protected $model = Produits::class;

    public function definition(): array
    {
        $price = fake()->randomFloat(2, 10, 2000);

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => $price,
            'sale_price' => null,
            'stock' => fake()->numberBetween(0, 50),
            'sku' => fake()->unique()->bothify('PRD-#####'),
            'category_id' => null,
            'brand_id' => null,
            'is_active' => true,
        ];
    }
}
