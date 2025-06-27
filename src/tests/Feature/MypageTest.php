<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class MypageTest extends TestCase
{
    use RefreshDatabase;

    // 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
    public function test_mypage_displays_user_data()
    {
        $user = User::factory()
            ->hasUserProfile(['profile_image' => 'dummy.jpg'])
            ->hasProducts(2)
            ->create();
        $purchasedProduct = Product::factory()->create();
        Purchase::factory()->create([
            'user_id' => $user->id,
            'product_id' => $purchasedProduct->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('mypage.index'));
        $response->assertStatus(200);
        $response->assertSee($user->user_name);
        $response->assertSee($user->userProfile->user_image); // 画像のURLが含まれるか
        $response->assertSee($user->products[0]->product_name); // 出品商品
        $response->assertSee($purchasedProduct->product_name); // 購入商品
    }

    // 
    public function test_profile_edit_form_has_old_values()
    {
        $user = User::factory()->create();
        $user->userProfile()->create([
            'postal_code' => '111-2222',
            'address' => '東京都テスト区',
            'building' => 'テストビル101',
            'profile_image' => 'test.jpg',
        ]);

        $this->actingAs($user);
        $response = $this->get(route('mypage.edit'));

        $response->assertStatus(200);
        $response->assertSee('111-2222');
        $response->assertSee('東京都テスト区');
        $response->assertSee('テストビル101');
        $response->assertSee('test.jpg');
        $response->assertSee($user->user_name);
    }

    // 


}
