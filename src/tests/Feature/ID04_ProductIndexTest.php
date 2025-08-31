<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Trade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID04_ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 仕様:
     * - 全商品が表示される（ゲスト）
     * - 「購入済み商品」= 取引進行/完了のものに Sold ラベル
     * - ログイン時は自分が出品した商品は表示されない
     *
     * 実装前提:
     * - ItemController@index は tab=recommend で
     *   withExists(['trades as has_active_trade' => whereIn(status, ['trading','buyer_completed','completed'])])
     *   を付与している
     */

    #[Test]
    public function it_displays_all_products_for_guest(): void
    {
        $products = Product::factory()->count(3)->create();

        $res = $this->get(route('items.index')); // GET '/' と同義
        $res->assertOk();

        foreach ($products as $p) {
            $res->assertSee(e($p->product_name));
        }
    }

    #[Test]
    public function it_shows_sold_badge_only_for_products_with_active_or_completed_trades(): void
    {
        // Arrange
        $seller = \App\Models\User::factory()->create();
        $buyer  = \App\Models\User::factory()->create();

        $sold = \App\Models\Product::factory()->create([
            'user_id'      => $seller->id,
            'product_name' => 'SoldItemName',
        ]);

        \App\Models\Trade::factory()->create([
            'product_id' => $sold->id,
            'seller_id'  => $seller->id,
            'buyer_id'   => $buyer->id,
            'status'     => 'completed', // trading / buyer_completed / completed のどれでもOK
        ]);

        $unsoldA = \App\Models\Product::factory()->create(['product_name' => 'UnsoldA']);
        $unsoldB = \App\Models\Product::factory()->create(['product_name' => 'UnsoldB']);

        // Act
        $res = $this->get(route('items.index', ['tab' => 'recommend']));
        $res->assertOk()
            ->assertSee(e($sold->product_name))
            ->assertSee(e($unsoldA->product_name))
            ->assertSee(e($unsoldB->product_name));

        $html = $res->getContent();

        // “Sold” は売れた商品のカード近傍にだけ出ることを確認（名前の直後~一定範囲内をチェック）
        $soldPos = strpos($html, e($sold->product_name));
        $unsoldAPos = strpos($html, e($unsoldA->product_name));
        $unsoldBPos = strpos($html, e($unsoldB->product_name));

        $this->assertNotFalse($soldPos, 'sold product name not found in HTML');
        $this->assertNotFalse($unsoldAPos, 'unsoldA name not found in HTML');
        $this->assertNotFalse($unsoldBPos, 'unsoldB name not found in HTML');

        // 商品名の周辺だけをそれぞれ抜き出して検査（他所の "Sold" をノーカウントにする）
        $window = 400; // 商品名の後ろ400文字くらいをチェック（カード内に十分収まる想定）
        $soldChunk    = substr($html, $soldPos, $window);
        $unsoldAChunk = substr($html, $unsoldAPos, $window);
        $unsoldBChunk = substr($html, $unsoldBPos, $window);

        $this->assertStringContainsString('Sold', $soldChunk, 'Sold badge should appear near the sold product.');
        $this->assertStringNotContainsString('Sold', $unsoldAChunk, 'Sold badge should not appear near unsoldA.');
        $this->assertStringNotContainsString('Sold', $unsoldBChunk, 'Sold badge should not appear near unsoldB.');
    }


    #[Test]
    public function it_hides_my_own_products_when_authenticated(): void
    {
        $me = User::factory()->create();

        $mineA = Product::factory()->create([
            'user_id'      => $me->id,
            'product_name' => 'My Item A',
        ]);
        $mineB = Product::factory()->create([
            'user_id'      => $me->id,
            'product_name' => 'My Item B',
        ]);

        $others = Product::factory()->count(2)->create();

        $this->actingAs($me);
        $res = $this->get(route('items.index', ['tab' => 'recommend']));

        $res->assertOk()
            ->assertDontSee($mineA->product_name)
            ->assertDontSee($mineB->product_name);

        foreach ($others as $p) {
            $res->assertSee(e($p->product_name));
        }
    }
}

