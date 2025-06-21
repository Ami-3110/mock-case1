<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductCategorySeeder extends Seeder
{
    public function run(){
        Product::find(14)->categories()->syncWithoutDetaching([1, 5]); // 腕時計
        Product::find(15)->categories()->syncWithoutDetaching([2]);     // HDD
        Product::find(16)->categories()->syncWithoutDetaching([10, 11]); // 玉ねぎ
        Product::find(17)->categories()->syncWithoutDetaching([1, 5]);     // 革靴
        Product::find(18)->categories()->syncWithoutDetaching([2]); // ノートPC
        Product::find(19)->categories()->syncWithoutDetaching([2]);     // マイク
        Product::find(20)->categories()->syncWithoutDetaching([1, 4]); // ショルダーバッグ
        Product::find(21)->categories()->syncWithoutDetaching([10]);     // タンブラー
        Product::find(22)->categories()->syncWithoutDetaching([3, 10]); // コーヒーミル
        Product::find(23)->categories()->syncWithoutDetaching([1, 4]);     // メイクセット

    }
    
}
