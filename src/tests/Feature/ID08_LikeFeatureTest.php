<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID08_LikeFeatureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_like_a_product_via_api()
    {
        $me = User::factory()->create();
        $product = Product::factory()->create([
            'product_name' => 'Like Target',
            'user_id' => User::factory(), // 出品者は他人
        ]);

        // 1) ログイン
        $this->actingAs($me);

        // 2) 詳細ページ（初期は未いいね = グレーアイコン想定）
        $resShow = $this->get(route('item.show', ['item_id' => $product->id]));
        $resShow->assertSuccessful();
        $resShow->assertSee('images/like.png');

        // 3) いいね追加（POST）
        $resPost = $this->post(route('like.store', ['item_id' => $product->id]));
        $resPost->assertOk()
                ->assertJson(['success' => true])
                ->assertJsonStructure(['likes_count']);

        // 4) 再読込するとアクティブアイコンに変化
        $resReload = $this->get(route('item.show', ['item_id' => $product->id]));
        $resReload->assertSuccessful();
        $resReload->assertSee('images/like-active.png');
    }

    #[Test]
    public function liked_icon_turns_active_after_like()
    {
        $me = User::factory()->create();
        $product = Product::factory()->create([
            'product_name' => 'Icon Check',
            'user_id' => User::factory(),
        ]);

        $this->actingAs($me);

        // 初期は非アクティブ
        $this->get(route('item.show', ['item_id' => $product->id]))
             ->assertSee('images/like.png');

        // いいね → 再表示でアクティブに
        $this->post(route('like.store', ['item_id' => $product->id]))->assertOk();
        $this->get(route('item.show', ['item_id' => $product->id]))
             ->assertSee('images/like-active.png');
    }

    #[Test]
    public function user_can_unlike_a_product_via_api()
    {
        $me = User::factory()->create();
        $product = Product::factory()->create([
            'product_name' => 'Unlike Target',
            'user_id' => User::factory(),
        ]);

        $this->actingAs($me);

        // 事前にいいねしておく
        $this->post(route('like.store', ['item_id' => $product->id]))->assertOk();
        $this->get(route('item.show', ['item_id' => $product->id]))
             ->assertSee('images/like-active.png');

        // 解除（DELETE）
        $resDel = $this->delete(route('like.destroy', ['item_id' => $product->id]));
        $resDel->assertOk()
               ->assertJson(['success' => true])
               ->assertJsonStructure(['likes_count']);

        // 再読込で非アクティブに戻る
        $this->get(route('item.show', ['item_id' => $product->id]))
        ->assertSee('images/like.png');
    }
}
