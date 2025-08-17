<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID11_PaymentMethodSelectionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function selected_payment_method_is_reflected_on_summary_initially()
    {
        $user = User::factory()->create();
        $item = Product::factory()->create(['price' => 12345]);

        $this->actingAs($user);

        // フラッシュ済み入力（old input）として支払い方法をセット
        $res = $this->withSession([
            '_old_input' => ['payment_method' => 'カード支払い'],
        ])->get(route('purchase.showForm', ['item_id' => $item->id]));

        $res->assertSuccessful();
        // サマリーに選択中の支払い方法が表示される（SSRで埋まる）
        $res->assertSee('カード支払い');
        // 小計はそのまま
        $res->assertSee('¥' . number_format($item->price));
    }
}
