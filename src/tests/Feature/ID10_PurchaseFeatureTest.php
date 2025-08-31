<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID10_PurchaseFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(), // verified 必須
        ]);
    }

    #[Test]
    public function purchasing_creates_purchase_record_and_redirects_thanks(): void
    {
        $buyer  = $this->makeVerifiedUser();
        $seller = $this->makeVerifiedUser();

        $product = Product::factory()->create([
            'user_id'      => $seller->id,
            'product_name' => 'ToBePurchased',
            // ★ is_sold は使わない
        ]);

        $this->actingAs($buyer);

        // 1) 住所登録（セッション保存）
        $this->post(route('purchase.updateAddress', ['item_id' => $product->id]), [
            'ship_postal_code' => '123-4567',
            'ship_address'     => '東京都千代田区1-1-1',
            'ship_building'    => 'テストビル101',
        ])->assertStatus(302);

        // 2) 購入実行
        $res = $this->post(route('purchase.confirm', ['item_id' => $product->id]), [
            'payment_method' => 'カード支払い',
        ]);

        // 完了画面へ
        $res->assertRedirect(route('purchase.thanks'));

        // 3) 購入レコードが作成されている
        $this->assertDatabaseHas('purchases', [
            'user_id'        => $buyer->id,
            'product_id'     => $product->id,
            'payment_method' => 'カード支払い',
        ]);
    }

    #[Test]
    public function purchased_product_shows_sold_badge_in_index(): void
    {
        $buyer  = $this->makeVerifiedUser();
        $seller = $this->makeVerifiedUser();

        $product = Product::factory()->create([
            'user_id'      => $seller->id,
            'product_name' => 'SoldBadgeCheck',
        ]);

        $this->actingAs($buyer);

        // 住所登録
        $this->post(route('purchase.updateAddress', ['item_id' => $product->id]), [
            'ship_postal_code' => '123-4567',
            'ship_address'     => '東京都千代田区1-1-1',
            'ship_building'    => null,
        ])->assertStatus(302);

        // 購入
        $this->post(route('purchase.confirm', ['item_id' => $product->id]), [
            'payment_method' => 'カード支払い',
        ])->assertRedirect(route('purchase.thanks'));

        // 一覧（tab 指定なし= recommend）で Sold 表示確認（誤検知回避の近傍チェック）
        $res = $this->get(route('items.index'));
        $res->assertOk()->assertSee(e('SoldBadgeCheck'));

        $html = $res->getContent();
        $pos  = strpos($html, e('SoldBadgeCheck'));
        $this->assertNotFalse($pos, 'product name not found in HTML');

        $chunk = substr($html, $pos, 400); // 商品カード内を想定して周辺を確認
        $this->assertTrue((bool)preg_match('/sold/i', $chunk), 'Sold badge should appear near the purchased product.');
    }

    #[Test]
    public function purchased_product_appears_in_profile_purchased_list(): void
    {
        $buyer  = $this->makeVerifiedUser();
        $seller = $this->makeVerifiedUser();

        $product = Product::factory()->create([
            'user_id'      => $seller->id,
            'product_name' => 'AppearsInProfile',
        ]);

        $this->actingAs($buyer);

        // 住所登録
        $this->post(route('purchase.updateAddress', ['item_id' => $product->id]), [
            'ship_postal_code' => '123-4567',
            'ship_address'     => '東京都千代田区1-1-1',
            'ship_building'    => 'テストビル202',
        ])->assertStatus(302);

        // 購入
        $this->post(route('purchase.confirm', ['item_id' => $product->id]), [
            'payment_method' => 'カード支払い',
        ])->assertRedirect(route('purchase.thanks'));

        // マイページ購入一覧に出る（/mypage?tab=buy 想定）
        $profile = $this->get(route('mypage.index', ['tab' => 'buy']));
        $profile->assertOk()->assertSee('AppearsInProfile');
    }
}

