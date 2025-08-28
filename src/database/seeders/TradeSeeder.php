<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trade;
use App\Models\Product;
use App\Models\User;

class TradeSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::take(5)->get(); // 適当に最初の5商品
        $users = User::pluck('id')->all();

        foreach ($products as $product) {
            // 出品者を商品作成者に、購入者をランダムに設定
            $sellerId = $product->user_id;
            $buyerId = collect($users)->reject(fn($id) => $id === $sellerId)->random();

            Trade::create([
                'product_id' => $product->id,
                'buyer_id'   => $buyerId,
                'seller_id'  => $sellerId,
                'status'     => 'trading',
            ]);
        }
    }
}
