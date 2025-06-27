<?php

namespace Database\Factories;

use App\Models\Like;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class LikeFactory extends Factory
{

    protected $model = Like::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
        ];
    }
}
