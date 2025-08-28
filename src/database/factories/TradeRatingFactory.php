<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TradeRating;

/** @extends Factory<\App\Models\TradeRating> */
class TradeRatingFactory extends Factory
{
    protected $model = TradeRating::class;

    public function definition(): array
    {
        return [
            'score' => $this->faker->numberBetween(1,5),
        ];
    }
}
