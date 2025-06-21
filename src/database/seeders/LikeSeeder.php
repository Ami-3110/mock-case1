<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Like;
use App\Models\User;
use App\Models\Product;

class LikeSeeder extends Seeder
{
    public function run()
    {
        $user = User::first(); 
        $products = Product::inRandomOrder()->take(5)->get(); 

        foreach ($products as $product) {
            Like::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
        }
    }
}
