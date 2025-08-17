<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID07_ProductShowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function product_detail_page_displays_all_required_information()
    {
        // Arrange: 出品者
        $seller = User::factory()->create();

        // 商品作成
        $product = Product::factory()->create([
            'user_id'       => $seller->id,
            'product_name'  => 'TestProduct',
            'brand'         => 'TestBrand',
            'price'         => 12345,
            'description'   => 'This is a test description.',
            'condition'     => '良好',
            'product_image' => 'default.jpg',
        ]);

        // いいねを複数追加
        Like::factory()->count(3)->create(['product_id' => $product->id]);

        // コメントを複数追加
        $commentUser1 = User::factory()->create(['user_name' => 'Alice']);
        $commentUser2 = User::factory()->create(['user_name' => 'Bob']);

        Comment::factory()->create([
            'user_id'    => $commentUser1->id,
            'product_id' => $product->id,
            'comment'    => 'Nice product!',
        ]);
        Comment::factory()->create([
            'user_id'    => $commentUser2->id,
            'product_id' => $product->id,
            'comment'    => 'Looks good!',
        ]);

        // Act: 商品詳細ページへアクセス
        $res = $this->get(route('item.show', ['item_id' => $product->id]));

        // Assert: 必要な情報がページに含まれているか
        $res->assertSuccessful();
        $res->assertSee('TestProduct');                     // 商品名
        $res->assertSee('TestBrand');                       // ブランド
        $res->assertSee('¥12,345');                         // 価格
        $res->assertSee('This is a test description.');     // 説明
        $res->assertSee('良好');                             // 状態
        $res->assertSee('default.jpg');                     // 商品画像のパス（実際のimgタグ確認ならassertSeeInOrderでもOK）

        // いいね数（Bladeでカウント表示していることを想定）
        $res->assertSee((string) 3);

        // コメント数
        $res->assertSee((string) 2);

        // コメント内容とユーザー名
        $res->assertSee('Alice');
        $res->assertSee('Nice product!');
        $res->assertSee('Bob');
        $res->assertSee('Looks good!');
    }

    #[Test]
    public function item_detail_page_shows_all_selected_categories()
    {
        // カテゴリを複数用意
        $catA = Category::factory()->create(['category_name' => 'カテゴリA']);
        $catB = Category::factory()->create(['category_name' => 'カテゴリB']);
        $catC = Category::factory()->create(['category_name' => 'カテゴリC']);

        // 商品作成
        $product = Product::factory()->create([
            'product_name' => 'カテゴリ表示テスト商品',
            'price'        => 1234,
        ]);

        // 中間テーブルに紐付け（AとCを選択）
        $product->categories()->attach([$catA->id, $catC->id]);

        // 商品詳細ページへ
        $res = $this->get(route('item.show', ['item_id' => $product->id]));
        $res->assertSuccessful();

        // 選択したカテゴリが表示される
        $res->assertSee('カテゴリA');
        $res->assertSee('カテゴリC');

        // 選択していないカテゴリは表示されない（任意チェック）
        $res->assertDontSee('カテゴリB');

        // 見出しなど（任意）
        $res->assertSee('カテゴリー');
        $res->assertSee('カテゴリ表示テスト商品');
    }
}
