<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductCategorySeeder extends Seeder
{
    public function run(){
        $this->attachCategories('腕時計', [1, 5]);
        $this->attachCategories('HDD', [2]);
        $this->attachCategories('玉ねぎ３束', [10, 11]);
        $this->attachCategories('革靴', [1, 5]);
        $this->attachCategories('ノートPC', [2]);
        $this->attachCategories('マイク', [2]);
        $this->attachCategories('ショルダーバッグ', [1, 4]);
        $this->attachCategories('タンブラー', [10]);
        $this->attachCategories('コーヒーミル', [3, 10]);
        $this->attachCategories('メイクセット', [1, 4]);
    }

    private function attachCategories(string $productName, array $categoryIds){
        $product = \App\Models\Product::where('product_name', $productName)->first();

        if ($product) {
            $product->categories()->syncWithoutDetaching($categoryIds);
        } else {
            echo "Product not found: {$productName}\n";
        }
    }
}
