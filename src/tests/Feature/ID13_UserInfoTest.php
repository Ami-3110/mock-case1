<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
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
        $user        = $this->makeVerifiedUser();
        $otherSeller = $this->makeVerifiedUser();

        $user->user_name = 'Ami Tester';
        $user->save();

        // 必須カラムありのプロフィール作成
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
            'is_sold'      => false,
        ]);
        $listed2 = Product::factory()->create([
            'user_id'      => $user->id,
            'product_name' => 'My Listed Item 2',
            'price'        => 3400,
            'is_sold'      => true, // soldラベル出る想定
        ]);

        // 購入した商品（buyタブで表示）
        $purchasedProduct = Product::factory()->create([
            'user_id'      => $otherSeller->id,
            'product_name' => 'Item I Bought',
            'price'        => 5600,
            'is_sold'      => true,
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

        $this->actingAs($user);

        // === sellタブ: 出品した商品が見える ===
        $resSell = $this->get(route('mypage.index', ['tab' => 'sell']));
        $resSell->assertSuccessful();
        // ヘッダー（ユーザー名/画像）は常に見える
        $resSell->assertSee('Ami Tester');
        if (method_exists($user, 'userProfile')) {
            $resSell->assertSee('profiles/test-avatar.jpg'); // 断片一致でOK
        }
        // 出品一覧の検証
        $resSell->assertSee('出品した商品');       // タブ見出し
        $resSell->assertSee('My Listed Item 1');
        $resSell->assertSee('My Listed Item 2');
        $resSell->assertSee('sold');               // soldラベル（2つ目がsold）

        // === buyタブ: 購入した商品が見える ===
        $resBuy = $this->get(route('mypage.index', ['tab' => 'buy']));
        $resBuy->assertSuccessful();
        // ヘッダー（ユーザー名/画像）は同様に見える
        $resBuy->assertSee('Ami Tester');
        if (method_exists($user, 'userProfile')) {
            $resBuy->assertSee('profiles/test-avatar.jpg');
        }
        // 購入一覧の検証
        $resBuy->assertSee('購入した商品');        // タブ見出し
        $resBuy->assertSee('Item I Bought');
        $resBuy->assertSee('sold');               // 買った商品が売却済みなら sold ラベルも出る
    }
}
