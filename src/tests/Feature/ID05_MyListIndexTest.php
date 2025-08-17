<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID05_MyListIndexTest extends TestCase
{
    use RefreshDatabase;

    private array $mylistQuery = ['tab' => 'mylist'];

    #[Test]
    public function only_liked_products_are_listed_for_authenticated_user()
    {
        $me = User::factory()->create();

        $liked = collect([
            Product::factory()->create(['product_name' => 'LIKED_ITEM_001']),
            Product::factory()->create(['product_name' => 'LIKED_ITEM_002']),
        ]);

        $unliked = collect([
            Product::factory()->create(['product_name' => 'UNLIKED_ITEM_101']),
            Product::factory()->create(['product_name' => 'UNLIKED_ITEM_102']),
        ]);

        foreach ($liked as $p) {
            \DB::table('likes')->insert([
                'user_id'    => $me->id,
                'product_id' => $p->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->actingAs($me);
        $res = $this->get(route('items.index', ['tab' => 'mylist']));

        $res->assertSuccessful();
        foreach ($liked as $p) {
            $res->assertSee(e($p->product_name));
        }
        foreach ($unliked as $p) {
            $res->assertDontSee(e($p->product_name));
        }
    }

    #[Test]
    public function purchased_products_show_sold_badge_in_mylist()
    {
        // Arrange
        $me = User::factory()->create();

        $sold  = Product::factory()->create(['is_sold' => true,  'product_name' => 'SoldInMyList']);
        $other = Product::factory()->create(['is_sold' => false, 'product_name' => 'OtherInMyList']);

        \DB::table('likes')->insert([
            'user_id'    => $me->id,
            'product_id' => $sold->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \DB::table('likes')->insert([
            'user_id'    => $me->id,
            'product_id' => $other->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Act
        $this->actingAs($me);
        $res = $this->get(route('items.index', $this->mylistQuery));

        // Assert
        $res->assertSuccessful();
        $res->assertSee(e('SoldInMyList'));
        $res->assertSee('sold');
        $res->assertSee(e('OtherInMyList'));
    }

    #[Test]
    public function my_own_products_are_hidden_in_mylist()
    {
        // Arrange
        $me = User::factory()->create();

        $mine = Product::factory()->create(['user_id' => $me->id, 'product_name' => 'MineLike']);
        $o1   = Product::factory()->create(['product_name' => 'OtherLike1']);
        $o2   = Product::factory()->create(['product_name' => 'OtherLike2']);

        \DB::table('likes')->insert([
            'user_id'    => $me->id,
            'product_id' => $mine->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \DB::table('likes')->insert([
            'user_id'    => $me->id,
            'product_id' => $o1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \DB::table('likes')->insert([
            'user_id'    => $me->id,
            'product_id' => $o2->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Act
        $this->actingAs($me);
        $res = $this->get(route('items.index', $this->mylistQuery));

        // Assert
        $res->assertSuccessful();
        $res->assertDontSee('MineLike');
        $res->assertSee('OtherLike1');
        $res->assertSee('OtherLike2');
    }

    #[Test]
    public function guest_sees_empty_mylist()
    {
        $someone = User::factory()->create();
        $p = Product::factory()->create(['product_name' => 'LikedByOtherUser']);
        \DB::table('likes')->insert([
            'user_id'    => $someone->id,
            'product_id' => $p->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Act（ゲスト）
        $res = $this->get(route('items.index', $this->mylistQuery));

        // Assert
        $res->assertSuccessful();
        $res->assertDontSee('LikedByOtherUser'); 
    }
}
