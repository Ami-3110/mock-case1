<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class ID15_ProductCreateTest extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function submitting_create_form_saves_required_fields_and_categories_with_image()
    {
        Storage::fake('public'); // ストレージをモック

        $user = $this->makeVerifiedUser();
        $this->actingAs($user);

        // カテゴリを2つ用意
        $cat1 = Category::factory()->create(['category_name' => 'カテゴリA']);
        $cat2 = Category::factory()->create(['category_name' => 'カテゴリB']);

        // ダミー画像を用意
        $fakeImage = UploadedFile::fake()->image('product.jpg');

        // 入力データ
        $payload = [
            'category'     => [$cat1->id, $cat2->id], // checkbox: category[]
            'condition'    => '良好',
            'product_name' => 'テスト出品タイトル',
            'brand'        => 'ブランド名（任意）',
            'description'  => '説明テキストです。',
            'price'        => 19800,
            'product_image'=> $fakeImage, // 必須の画像
        ];

        // 送信
        $res = $this->post(route('items.store'), $payload);

        // 成功（基本リダイレクト）
        $res->assertStatus(302);
        $res->assertSessionHasNoErrors();

        // DBに商品が保存されていること
        $this->assertDatabaseHas('products', [
            'product_name' => 'テスト出品タイトル',
            'description'  => '説明テキストです。',
            'price'        => 19800,
            'condition'    => '良好',
            'user_id'      => $user->id,
        ]);

        // 保存された商品を取得
        $product = Product::where('product_name', 'テスト出品タイトル')->firstOrFail();

        // ストレージに画像が保存されていること
        Storage::disk('public')->assertExists($product->product_image);

        // ピボットテーブルにカテゴリが紐づいていること
        $this->assertDatabaseHas('product_category', [
            'product_id'  => $product->id,
            'category_id' => $cat1->id,
        ]);
        $this->assertDatabaseHas('product_category', [
            'product_id'  => $product->id,
            'category_id' => $cat2->id,
        ]);
    }
}
