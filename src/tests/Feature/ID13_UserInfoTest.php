<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Trade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID13_UserInfoTest extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function profile_page_shows_avatar_username_and_lists_per_tab()
    {
        // 準備
        $user          = $this->makeVerifiedUser();
        $otherSeller   = $this->makeVerifiedUser();
        $anotherBuyer  = $this->makeVerifiedUser();

        $user->user_name = 'Ami Tester';
        $user->save();

        // プロフィール（任意）
        if (method_exists($user, 'userProfile')) {
            $user->userProfile()->create([
                'profile_image' => 'profiles/test-avatar.jpg',
                'postal_code'   => '100-0001',
                'address'       => '東京都千代田区千代田1-1',
                'building'      => 'テストビル101',
            ]);
        }

        // 出品した商品（sellタブで表示）
        $listed1 = Product::factory()->create([
            'user_id'      => $user->id,
            'product_name' => 'My Listed Item 1',
            'price'        => 1200,
        ]);
        $listed2 = Product::factory()->create([
            'user_id'      => $user->id,
            'product_name' => 'My Listed Item 2',
            'price'        => 3400,
        ]);
        // listed2 は取引あり（= sold 扱い）
        Trade::factory()->create([
            'product_id' => $listed2->id,
            'seller_id'  => $user->id,
            'buyer_id'   => $anotherBuyer->id,
            'status'     => 'completed', // trading / buyer_completed / completed でもOK
        ]);

        // 購入した商品（buyタブで表示）
        $purchasedProduct = Product::factory()->create([
            'user_id'      => $otherSeller->id,
            'product_name' => 'Item I Bought',
            'price'        => 5600,
        ]);
        Purchase::create([
            'user_id'          => $user->id, // buyer
            'product_id'       => $purchasedProduct->id,
            'payment_method'   => 'カード支払い',
            'ship_postal_code' => '100-0001',
            'ship_address'     => '東京都千代田区千代田1-1',
            'ship_building'    => 'テスト101',
            'purchased_at'     => now(),
        ]);
        // （任意）trades を作ってもOKだが、buyタブでは sold バッジ検証は行わない
        Trade::factory()->create([
            'product_id' => $purchasedProduct->id,
            'seller_id'  => $otherSeller->id,
            'buyer_id'   => $user->id,
            'status'     => 'completed',
        ]);

        $this->actingAs($user);

        // === sellタブ ===
        $resSell = $this->get(route('mypage.index', ['tab' => 'sell']));
        $resSell->assertOk();
        $resSell->assertSee('Ami Tester');
        if (method_exists($user, 'userProfile')) {
            $resSell->assertSee('profiles/test-avatar.jpg');
        }
        $resSell->assertSee('出品した商品');
        $resSell->assertSee('My Listed Item 1');
        $resSell->assertSee('My Listed Item 2');

        // sold 表示は listed2 の近傍のみ（ケース無視）
        $htmlSell = $resSell->getContent();
        $l1Pos = strpos($htmlSell, 'My Listed Item 1');
        $l2Pos = strpos($htmlSell, 'My Listed Item 2');
        $this->assertNotFalse($l1Pos);
        $this->assertNotFalse($l2Pos);
        $win = 400;
        $l1Chunk = substr($htmlSell, $l1Pos, $win);
        $l2Chunk = substr($htmlSell, $l2Pos, $win);
        $this->assertFalse((bool)preg_match('/sold/i', $l1Chunk), 'sold should NOT appear near My Listed Item 1');
        $this->assertTrue((bool)preg_match('/sold/i', $l2Chunk),  'sold should appear near My Listed Item 2');

        // === buyタブ ===（sold バッジ検証はしない）
        $resBuy = $this->get(route('mypage.index', ['tab' => 'buy']));
        $resBuy->assertOk();
        $resBuy->assertSee('Ami Tester');
        if (method_exists($user, 'userProfile')) {
            $resBuy->assertSee('profiles/test-avatar.jpg');
        }
        $resBuy->assertSee('購入した商品');
        $resBuy->assertSee('Item I Bought');
    }
}
