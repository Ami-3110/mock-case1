<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    // 「購入する」ボタン押下で購入が完了し、DBに購入情報が登録される
    public function test_user_can_complete_purchase()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_sold' => false]);
    
        $this->actingAs($user)
             ->withSession([
                 'shipping_address_' . $product->id => [
                     'ship_postal_code' => '987-6543',
                     'ship_address' => '東京都渋谷区2-2-2',
                     'ship_building' => 'タワー202',
                 ],
             ]);
    
        // リクエスト送信
        $response = $this->get("/purchase/stripe/{$product->id}");
    
        // StripeのリダイレクトURLに転送されるので、302でOK
        $response->assertStatus(302);
    
        // DBに登録されたか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method' => 'カード支払い',
            'ship_postal_code' => '987-6543',
            'ship_address' => '東京都渋谷区2-2-2',
            'ship_building' => 'タワー202',
        ]);
    
        $isSold = DB::table('products')->where('id', $product->id)->value('is_sold');
        $this->assertTrue((bool)$isSold, '商品が is_sold に更新されていません');
    }
    

    // 購入した商品は商品一覧画面にて「sold」と表示される
    public function test_purchased_product_is_marked_as_sold_in_product_list()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_sold' => true]);
        $this->actingAs($user);

        $response = $this->get(route('items.index'));

        $response->assertSee('sold');
        $response->assertSee($product->product_name);
    }

    // 「プロフィール/購入した商品一覧」に追加されている
    public function test_purchased_product_appears_in_user_purchase_list()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_sold' => true]);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method' => 'カード支払い',
            'ship_postal_code' => '123-4567',
            'ship_address' => '東京都新宿区1-1-1',
            'ship_building' => 'ビル101',
        ]);
        $this->actingAs($user);

        $response = $this->get(route('mypage.index', ['tab' => 'buy']));

        $response->assertSee($product->product_name);
    }

    // 送付先住所変更画面で登録した住所が商品購入画面に反映されている
    public function test_shipping_address_changes_reflect_on_purchase_form()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_sold' => false]);
        $this->actingAs($user);

        $response = $this->post(route('purchase.updateAddress', ['item_id' => $product->id]), [
            'ship_postal_code' => '987-6543',
            'ship_address' => '大阪府大阪市2-2-2',
            'ship_building' => 'マンション202',
        ]);
        $response->assertRedirect(route('purchase.showForm', ['item_id' => $product->id]));

        
        $response = $this->get(route('purchase.showForm', ['item_id' => $product->id]));
        $response->assertSee('987-6543');
        $response->assertSee('大阪府大阪市2-2-2');
        $response->assertSee('マンション202');
    }

    // 購入した商品に送付先住所が紐づいて登録される
    public function test_shipping_address_is_saved_with_purchase()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['is_sold' => false]);

        $this->actingAs($user)
            ->withSession([
                'shipping_address_' . $product->id => [
                    'ship_postal_code' => '111-2222',
                    'ship_address' => '北海道札幌市3-3-3',
                    'ship_building' => 'アパート303',
                ],
            ]);

        $this->get("/purchase/stripe/{$product->id}");

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'payment_method' => 'カード支払い',
            'ship_postal_code' => '111-2222',
            'ship_address' => '北海道札幌市3-3-3',
            'ship_building' => 'アパート303',
        ]);
    }

}