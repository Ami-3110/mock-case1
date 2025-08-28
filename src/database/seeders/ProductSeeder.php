<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // UserSeeder で作ったユーザーをメールで取得（確実）
        $user1 = User::where('email', 'user1@example.com')->firstOrFail();
        $user2 = User::where('email', 'user2@example.com')->firstOrFail();

        $items = [
            ['product_name' => '腕時計',        'price' => 15000, 'product_image' => 'products/product01.jpg', 'condition' => '良好',                 'description'=> 'スタイリッシュなデザインのメンズ腕時計'],
            ['product_name' => 'HDD',          'price' => 5000,  'product_image' => 'products/product02.jpg', 'condition' => '目立った傷や汚れなし', 'description'=> '高速で信頼性の高いハードディスク'],
            ['product_name' => '玉ねぎ３束',   'price' => 300,   'product_image' => 'products/product03.jpg', 'condition' => 'やや傷や汚れあり',     'description'=> '新鮮な玉ネギ３束のセット'],
            ['product_name' => '革靴',         'price' => 4000,  'product_image' => 'products/product04.jpg', 'condition' => '状態が悪い',           'description'=> 'クラシックなデザインの革靴'],
            ['product_name' => 'ノートPC',     'price' => 45000, 'product_image' => 'products/product05.jpg', 'condition' => '良好',                 'description'=> '高性能なノートパソコン'],
            ['product_name' => 'マイク',       'price' => 300,   'product_image' => 'products/product06.jpg', 'condition' => '目立った傷や汚れなし', 'description'=> '高音質のレコーディング用マイク'],
            ['product_name' => 'ショルダーバッグ','price'=>3500, 'product_image' => 'products/product07.jpg', 'condition' => 'やや傷や汚れあり',     'description'=> 'おしゃれなショルダーバッグ'],
            ['product_name' => 'タンブラー',   'price' => 500,   'product_image' => 'products/product08.jpg', 'condition' => '状態が悪い',           'description'=> '使いやすいタンブラー'],
            ['product_name' => 'コーヒーミル', 'price' => 4000,  'product_image' => 'products/product09.jpg', 'condition' => '良好',                 'description'=> '手動のコーヒーミル'],
            ['product_name' => 'メイクセット', 'price' => 2500,  'product_image' => 'products/product10.jpg', 'condition' => '目立った傷や汚れなし', 'description'=> '便利なメイクアップセット'],
        ];

        foreach ($items as $i => $data) {
            // 1〜5個目 → user1（まさちゃん）、6〜10個目 → user2（ゴマ）
            $data['user_id'] = $i < 5 ? $user1->id : $user2->id;

            Product::create($data);
        }
    }
}
