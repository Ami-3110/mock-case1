<?php

namespace Database\Seeders;

//use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); 
        
        Product::create([
            'product_name' => '腕時計',
            'price' => 15000,
            'product_image' => 'products/product01.jpg',
            'condition' => '良好',
            'description'=> 'スタイリッシュなデザインのメンズ腕時計',
            'user_id' => $user->id,
            'is_sold'=>false,
        ]);
        Product::create([
            'product_name' => 'HDD',
            'price' => 5000,
            'product_image' => 'products/product02.jpg',
            'condition' => '目立った傷や汚れなし',
            'description'=> '高速で信頼性の高いハードディスク',
            'user_id' => $user->id,
            'is_sold'=>false,
        ]);
        Product::create([
            'product_name' => '玉ねぎ３束',
            'price' => 300,
            'product_image' => 'products/product03.jpg',
            'condition' => 'やや傷や汚れあり',
            'description'=> '新鮮な玉ネギ３束のセット',
            'user_id' => $user->id,
            'is_sold'=>false,
        ]);
        Product::create([
            'product_name' => '革靴',
            'price' => 4000,
            'product_image' => 'products/product04.jpg',
            'condition' => '状態が悪い',
            'description'=> 'クラシックなデザインの革靴',
            'user_id' => $user->id,
            'is_sold'=>false,
        ]);
        Product::create([
            'product_name' => 'ノートPC',
            'price' => 45000,
            'product_image' => 'products/product05.jpg',
            'condition' => '良好',
            'description'=> '高性能なノートパソコン',
            'user_id' => $user->id,
            'is_sold'=>false,
        ]);
        Product::create([
            'product_name' => 'マイク',
            'price' => 300,
            'product_image' => 'products/product06.jpg',
            'condition' => '目立った傷や汚れなし',
            'description'=> '高音質のレコーディング用マイク',
            'user_id' => $user->id,
            'is_sold'=>false,
        ]);

        Product::create([
            'product_name'=>'ショルダーバッグ',
            'price'=>3500,
            'product_image' => 'products/product07.jpg',
            'condition' => 'やや傷や汚れあり',
            'description'=> 'おしゃれなショルダーバッグ',
            'user_id' => $user->id,
            'is_sold'=>false,
        ]);
        Product::create([
            'product_name' => 'タンブラー',
            'price' => 500,
            'product_image' => 'products/product08.jpg',
            'condition' => '状態が悪い',
            'description'=> '使いやすいタンブラー',
            'user_id' => $user->id,
            'is_sold'=>false,
        ]);
        Product::create([
            'product_name' => 'コーヒーミル',
            'price' => 4000,
            'product_image' => 'products/product09.jpg',
            'condition' => '良好',
            'description'=> '手動のコーヒーミル',
            'user_id' => $user->id,
            'is_sold'=>false,
        ]);
        Product::create([
            'product_name' => 'メイクセット',
            'price' => 2500,
            'product_image' => 'products/product10.jpg',
            'condition' => '目立った傷や汚れなし',
            'description'=> '便利なメイクアップセット',
            'user_id' => $user->id,
            'is_sold'=>false,
        ]);   
    }
}
