<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; // ← 追加

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) public ディスク上に必須ディレクトリを用意
        if (!Storage::disk('public')->exists('products')) {
            Storage::disk('public')->makeDirectory('products');
        }
        if (!Storage::disk('public')->exists('user_images')) {
            Storage::disk('public')->makeDirectory('user_images');
        }

        // 2) リポジトリ内の public/ 以下に元画像があるなら storage にコピー
        //    （なければこのブロックはスキップされるので安全）
        $fromProducts = public_path('products');
        $toProducts   = storage_path('app/public/products');
        if (File::isDirectory($fromProducts)) {
            File::ensureDirectoryExists($toProducts);
            foreach (File::files($fromProducts) as $file) {
                File::copy($file->getRealPath(), $toProducts.'/'.basename($file));
            }
        }

        $fromUsers = public_path('user_images');
        $toUsers   = storage_path('app/public/user_images');
        if (File::isDirectory($fromUsers)) {
            File::ensureDirectoryExists($toUsers);
            foreach (File::files($fromUsers) as $file) {
                File::copy($file->getRealPath(), $toUsers.'/'.basename($file));
            }
        }

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            ProductCategorySeeder::class,
            LikeSeeder::class,
            // TradeSeeder::class,
            // TradeMessageSeeder::class,
            // TradeRatingSeeder::class,
        ]);
    }
}
