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
    public function purchasing_marks_product_as_sold_and_creates_purchase_record()
    {
        $buyer  = $this->makeVerifiedUser();
        $seller = $this->makeVerifiedUser();

        $product = Product::factory()->create([
            'user_id'      => $seller->id,
            'product_name' => 'ToBePurchased',
            'is_sold'      => false,
            // 'price' が必要なら factory 側で用意されてるはず。無ければここで定義
            // 'price' => 1000,
        ]);

        $this->actingAs($buyer);

        // 1) 住所登録（セッションに保存）
        $this->post(route('purchase.updateAddress', ['item_id' => $product->id]), [
            'ship_postal_code' => '123-4567',
            'ship_address'     => '東京都千代田区1-1-1',
            'ship_building'    => 'テストビル101',
        ])->assertStatus(302);

        // 2) 購入実行（testing 環境は thanks にリダイレクト）
        $res = $this->post(route('purchase.confirm', ['item_id' => $product->id]), [
            'payment_method' => 'カード支払い', // ← バリデの in に合わせる（'card' ではNG）
        ]);

        $res->assertRedirect(route('purchase.thanks'));

        // 3) DB 反映確認
        $this->assertDatabaseHas('products', [
            'id'      => $product->id,
            'is_sold' => true,
        ]);

        $this->assertDatabaseHas('purchases', [
            'user_id'    => $buyer->id,
            'product_id' => $product->id,
            'payment_method' => 'カード支払い',
        ]);
    }

    #[Test]
    public function purchased_product_shows_sold_badge_in_index()
    {
        $buyer  = $this->makeVerifiedUser();
        $seller = $this->makeVerifiedUser();

        $product = Product::factory()->create([
            'user_id'      => $seller->id,
            'product_name' => 'SoldBadgeCheck',
            'is_sold'      => false,
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

        // 一覧に sold バッジが表示されること
        $res = $this->get(route('items.index')); // '/'
        $res->assertSuccessful();
        $res->assertSee('sold');               // ビュー側で 'sold' を出している想定
        $res->assertSee('SoldBadgeCheck');
    }

    #[Test]
    public function purchased_product_appears_in_profile_purchased_list()
    {
        $buyer  = $this->makeVerifiedUser();
        $seller = $this->makeVerifiedUser();

        $product = Product::factory()->create([
            'user_id'      => $seller->id,
            'product_name' => 'AppearsInProfile',
            'is_sold'      => false,
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

        // マイページ購入一覧に表示されること（/mypage?tab=buy の想定）
        $profile = $this->get(route('mypage.index', ['tab' => 'buy']));
        $profile->assertSuccessful();
        $profile->assertSee('AppearsInProfile');
    }
}

