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
            'user_id' => \App\Models\User::factory(),
            'product_name' => $this->faker->word(),
            'product_image' => 'default.jpg',
            'brand' => $this->faker->company(),
            'price' => $this->faker->numberBetween(1000, 100000),
            'description' => $this->faker->sentence(),
            'condition' => '良好',
            'is_sold' => false,
        ];
    }
}
