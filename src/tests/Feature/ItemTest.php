<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    // 全商品を取得できる
    public function test_canGetAllProducts()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    // 商品一覧で購入済み商品は「Sold」と表示される
    public function test_soldProductsAreMarkedAsSoldInProductList()
    {
        $soldProduct = \App\Models\Product::factory()->create([
            'is_sold' => true,
            'product_name' => '売れた商品',
        ]);
        $unsoldProduct = \App\Models\Product::factory()->create([
            'is_sold' => false,
            'product_name' => '売れてない商品',
        ]);
        $response = $this->get('/');
        $response->assertSee('sold');
        $response->assertSee('売れた商品');
        $response->assertSee('売れてない商品');
    }
    
    // 商品一覧で自分が出品した商品は表示されない
    public function test_myListedProductsAreNotDisplayedInProductList()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $productMine = \App\Models\Product::factory()->create([
            'user_id' => $user->id,
            'product_name' => '自分の商品',
        ]);

        $productOthers = \App\Models\Product::factory()->create([
            'product_name' => '他人の商品',
        ]);

        $response = $this->get('/');
        $response->assertDontSee('自分の商品');
        $response->assertSee('他人の商品');
    }

    // マイリストでいいねした商品だけが表示される
    public function test_onlyLikedProductsAreDisplayedInMyList()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
        
        $productLiked = Product::factory()->create(['product_name' => 'いいねした商品']);
        $productNotLiked = Product::factory()->create(['product_name' => 'してない商品']);

        \App\Models\Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $productLiked->id,
        ]);

        $response = $this->get('/?tab=mylist');
        $response->assertSee('いいねした商品');
        $response->assertDontSee('してない商品');

    }

    // マイリストで購入済み商品は「Sold」と表示される
    public function test_soldProductsAreMarkedAsSoldInMyList()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $soldProduct = \App\Models\Product::factory()->create([
            'is_sold' => true,
            'product_name' => '売れた商品',
        ]);
        $unsoldProduct = \App\Models\Product::factory()->create([
            'is_sold' => false,
            'product_name' => '売れてない商品',
        ]);

        \App\Models\Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $soldProduct->id,
        ]);
        \App\Models\Like::factory()->create([
            'user_id' => $user->id,
            'product_id' =>$unsoldProduct->id,
        ]);

        $response = $this->get('/?tab=mylist');
        $response->assertSee('売れた商品');
        $response->assertSee('sold');
        $response->assertSee('売れてない商品');
    }

    // マイリストで自分が出品した商品は表示されない
    public function test_myListedProductsAreNotDisplayedInMyList()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $productMine = \App\Models\Product::factory()->create([
            'user_id' => $user->id,
            'product_name' => '自分の商品',
        ]);
        $productOthers = \App\Models\Product::factory()->create([
            'product_name' => '他人の商品',
        ]);

        \App\Models\Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $productMine->id,
        ]);
        
        \App\Models\Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $productOthers->id,
        ]);
        
        
        $response = $this->get('/?tab=mylist');
        $response->assertDontSee('自分の商品');
        $response->assertSee('他人の商品');
    }

    // マイリストで未認証の場合は何も表示されない
    public function test_nothingIsDisplayedInMyListWhenUnauthenticated()
    {
        $product = \App\Models\Product::factory()->create([
            'product_name' => 'いいね商品',
        ]);

        $like = \App\Models\Like::factory()->create([
            'product_id' => $product->id,
        ]);

        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertDontSee('いいね商品');
    }

    
    // 商品一覧で「商品名」で部分一致検索ができる
    public function test_canSearchProductsByPartialNameInProductList()
    {
    $productMatch = \App\Models\Product::factory()->create([
        'product_name' => 'ダイビング重機材セット',
    ]);
    $productNoMatch = \App\Models\Product::factory()->create([
        'product_name' => '別の商品',
    ]);

    $response = $this->get(route('items.search', ['keyword' => 'ダイビング']));
    $response->assertSee('ダイビング重機材セット');
    $response->assertDontSee('別の商品');
    }
    
    // 検索状態がマイリストでも保持されている
    public function test_searchStateIsPreservedInMyList()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $productMatch = \App\Models\Product::factory()->create([
            'product_name' => 'ダイビング重機材セット',
        ]);
        $productNoMatch = \App\Models\Product::factory()->create([
            'product_name' => '別の商品',
        ]);

        \App\Models\Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $productMatch->id,
        ]);
        
        \App\Models\Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $productNoMatch->id,
        ]);       

        $response = $this->get('/?tab=mylist&keyword=ダイビング');
        $response->assertSee('ダイビング重機材セット');
        $response->assertDontSee('別の商品');
    }

    // 商品詳細で必要な情報が表示される
    public function test_requiredInformationIsDisplayedInProductDetail()
    {
        $user = \App\Models\User::factory()->create([
            'user_name' => 'コメントユーザー',
        ]);

        $product = \App\Models\Product::factory()->create([
            'product_image'=>'sample.jpg',
            'product_name' => 'テスト商品',
            'brand'=>'テストブランド',
            'price'=>'123456',
            'description' => 'これは商品の説明です。',
            'condition' => '良好',
        ]);
        $category = \App\Models\Category::factory()->create(['category_name' => 'スポーツ']);
        $product->categories()->attach($category->id);


        \App\Models\Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
        \App\Models\Comment::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'comment' => 'これはコメントです。',
        ]);
        $response = $this->get("/item/{$product->id}");
         // 商品の基本情報
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('¥123,456');
        $response->assertSee('これは商品の説明です。');
        $response->assertSee('スポーツ');
        $response->assertSee('良好');

        // 商品画像（画像のファイル名 or URL or alt属性）
        $response->assertSee('sample.jpg');

        // いいね数（具体的な数字でも「いいね1件」などでも）
        $response->assertSee('<span id="like-count" class="icon-count">1</span>', false);

        // コメント数
        $response->assertSee('<span id="comment-count" class="icon-count">1</span>',false);

        // コメント内容
        $response->assertSee('コメントユーザー');
        $response->assertSee('これはコメントです。');
    }

    // 商品詳細で複数選択されたカテゴリが表示されている
    public function test_multipleSelectedCategoriesAreDisplayedInProductDetail()
    {
            $product = \App\Models\Product::factory()->create();

            $category1 = \App\Models\Category::factory()->create(['category_name' => 'ダイビング']);
            $category2 = \App\Models\Category::factory()->create(['category_name' => 'アウトドア']);

            // カテゴリを商品に紐づける
            $product->categories()->attach([$category1->id, $category2->id]);

            // 商品詳細ページにアクセス
            $response = $this->get("/item/{$product->id}");

            // 両方のカテゴリ名が表示されていることを確認
            $response->assertSee('ダイビング');
            $response->assertSee('アウトドア');
    } 

    // 商品出品画面にて必要な情報が保存できること
    public function test_product_can_be_created_with_valid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/sell', [
            'product_name' => 'テスト商品',
            'description' => 'これはテスト用の商品です',   // product_detail → description に修正
            'category' => [Category::factory()->create()->id],
            'condition' => '新品',
            'price' => 3000,
            'product_image' => UploadedFile::fake()->create('test.jpg', 500, 'image/jpeg'),
        ]);

        $response->assertRedirect(); // or wherever it redirects to
        $this->assertDatabaseHas('products', ['product_name' => 'テスト商品']);
    }


}
