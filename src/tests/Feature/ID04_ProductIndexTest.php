<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID04_ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_all_products_for_guest()
    {
        $products = Product::factory()->count(3)->create();

        $res = $this->get(route('items.index')); // 実URLは '/'

        $res->assertSuccessful();
        foreach ($products as $p) {
            $res->assertSee(e($p->product_name));
        }
    }

    #[Test]
    public function purchased_products_show_sold_badge()
    {
        // is_sold=true の商品を用意（判定はフラグ方式）
        $sold  = Product::factory()->create([
            'is_sold' => true,
            'product_name' => 'SoldItemName',
        ]);
        $other = Product::factory()->create([
            'is_sold' => false,
            'product_name' => 'OtherItemName',
        ]);

        $res = $this->get(route('items.index'));

        $res->assertSuccessful();
        $res->assertSee('Sold');
        $res->assertSee(e($sold->product_name));
        $res->assertSee(e($other->product_name));
    }

    #[Test]
    public function my_own_products_are_hidden_when_logged_in()
    {
        $me   = User::factory()->create();
        $mine = Product::factory()->create([
            'user_id' => $me->id,
            'product_name' => 'My Item Name',
        ]);
        $others = Product::factory()->count(2)->create();

        $this->actingAs($me);
        $res = $this->get(route('items.index'));

        $res->assertSuccessful();
        $res->assertDontSee('My Item Name');
        foreach ($others as $p) {
            $res->assertSee(e($p->product_name));
        }
    }
}
