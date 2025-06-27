<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'profile_image' => 'default.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都港区1-1-1',
            'building' => 'サンプルビル101',
        ];
    }
}

