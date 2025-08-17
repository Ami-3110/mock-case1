<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID12_UpdateShippingTest extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function shipping_address_entered_in_edit_screen_is_reflected_on_purchase_screen()
    {
        $user = $this->makeVerifiedUser();
        $seller = $this->makeVerifiedUser();

        $item = Product::factory()->create([
            'user_id'      => $seller->id,
            'product_name' => 'Ship Target',
            'price'        => 12345,
        ]);

        $this->actingAs($user);

        // 1) 住所登録（セッションに保存）
        $this->post(route('purchase.updateAddress', ['item_id' => $item->id]), [
            'ship_postal_code' => '100-0001',
            'ship_address'     => '東京都千代田区千代田1-1',
            'ship_building'    => '皇居前テストビル101',
        ])->assertStatus(302);

        // 2) 購入画面を再表示 → 反映されていること
        $res = $this->get(route('purchase.showForm', ['item_id' => $item->id]));
        $res->assertSuccessful();
        $res->assertSee('100-0001');
        $res->assertSee('東京都千代田区千代田1-1');
        $res->assertSee('皇居前テストビル101');
        $res->assertSee('¥' . number_format($item->price));
    }

    #[Test]
    public function purchased_item_has_shipping_address_associated_in_purchases_table()
    {
        $buyer  = $this->makeVerifiedUser();
        $seller = $this->makeVerifiedUser();

        $item = Product::factory()->create([
            'user_id'      => $seller->id,
            'product_name' => 'Purchased With Shipping',
            'price'        => 5000,
            'is_sold'      => false,
        ]);

        $this->actingAs($buyer);

        // 1) 住所登録（セッションへ）
        $payload = [
            'ship_postal_code' => '150-0001',
            'ship_address'     => '東京都渋谷区神宮前1-1-1',
            'ship_building'    => 'テストマンション202',
        ];
        $this->post(route('purchase.updateAddress', ['item_id' => $item->id]), $payload)
             ->assertStatus(302);

        // 2) 購入実行（testing 環境は thanks にリダイレクト）
        $this->post(route('purchase.confirm', ['item_id' => $item->id]), [
            'payment_method' => 'コンビニ払い', // ← バリデ in: に一致
        ])->assertRedirect(route('purchase.thanks'));

        // 3) purchases に住所が紐づいて登録されている
        $this->assertDatabaseHas('purchases', [
            'user_id'          => $buyer->id,
            'product_id'       => $item->id,
            'payment_method'   => 'コンビニ払い',
            'ship_postal_code' => $payload['ship_postal_code'],
            'ship_address'     => $payload['ship_address'],
            'ship_building'    => $payload['ship_building'],
        ]);

        // 4) is_sold も true
        $this->assertDatabaseHas('products', [
            'id'      => $item->id,
            'is_sold' => true,
        ]);

        // 5) マイページ購入一覧に表示
        $this->get(route('mypage.index', ['tab' => 'buy']))
             ->assertSuccessful()
             ->assertSee('Purchased With Shipping');
    }
}
