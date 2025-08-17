<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID06_ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_search_products_by_partial_name()
    {
        // Arrange
        $p1 = Product::factory()->create(['product_name' => 'Apple Watch Band']);
        $p2 = Product::factory()->create(['product_name' => 'Pineapple Case']);
        $p3 = Product::factory()->create(['product_name' => 'Banana Holder']);

        // Act: /search に部分一致キーワード 'apple'
        $res = $this->get(route('items.search', ['keyword' => 'apple']));

        // Assert: Apple含む2件は表示、含まない1件は非表示
        $res->assertSuccessful();
        $res->assertSee(e($p1->product_name));
        $res->assertSee(e($p2->product_name));
        $res->assertDontSee(e($p3->product_name));
    }

    #[Test]
    public function search_keyword_is_preserved_when_switching_to_mylist_tab()
    {
        // Arrange
        $me  = User::factory()->create();

        // 他人の商品で、いいね済みの2件（うち1件だけがキーワード一致）
        $likedMatch = Product::factory()->create([
            'user_id' => User::factory(), 'product_name' => 'Diving Apple Mount'
        ]);
        $likedNoMatch = Product::factory()->create([
            'user_id' => User::factory(), 'product_name' => 'Diving Banana Mount'
        ]);

        // いいね付与
        \DB::table('likes')->insert([
            'user_id' => $me->id, 'product_id' => $likedMatch->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        \DB::table('likes')->insert([
            'user_id' => $me->id, 'product_id' => $likedNoMatch->id,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        // Act 1: ホームで検索（結果の中身まではここで断定しない。成功だけ見る）
        $this->actingAs($me);
        $home = $this->get(route('items.index', ['keyword' => 'apple']));
        $home->assertSuccessful();

        // Act 2: マイリストタブに遷移（keywordも付いたまま）
        $mylist = $this->get(route('items.index', ['tab' => 'mylist', 'keyword' => 'apple']));

        // Assert: いいね済みの中で "apple" を含むものだけ表示、含まないものは非表示
        $mylist->assertSuccessful();
        $mylist->assertSee(e($likedMatch->product_name));
        $mylist->assertDontSee(e($likedNoMatch->product_name));
    }
}
