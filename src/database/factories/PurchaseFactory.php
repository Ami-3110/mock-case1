<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Str;

class PurchaseFactory extends Factory
{
    protected $model = Purchase::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'payment_method' => 'カード支払い',
            'ship_postal_code' => '123-4567',
            'ship_address' => '東京都新宿区1-1-1',
            'ship_building' => 'テストビル101',
        ];
    }
};
