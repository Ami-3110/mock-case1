<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Like;
use App\Models\Comment;

class LikeCommentTest extends TestCase
{
    use RefreshDatabase;

    use RefreshDatabase;

    // いいねアイコンを押下することによって、いいねした商品として登録することができる。
    public function test_userCanLikeAProduct()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $this->assertEquals(0, $product->likes()->count());

        $response = $this->post("/like/{$product->id}");

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
        $this->assertEquals(1, $product->fresh()->likes()->count());
    }

    // 追加済みのアイコンは色が変化する
    public function test_likedProductIconHasLikedClass()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        \App\Models\Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->get("/item/{$product->id}"); 

        $response->assertSee('aria-pressed="true"', false);
        $response->assertSee('like-active.png', false);

    }

    // 再度いいねアイコンを押下することによって、いいねを解除することができる。
    public function test_removedlikedIcon()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        \App\Models\Like::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        \App\Models\Like::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->delete();

        $response = $this->get("/item/{$product->id}"); 

        $response->assertSee('aria-pressed="false"', false);
        $response->assertSee('like.png', false);

    }

    // ログイン済みのユーザーはコメントを送信できる
    public function test_userCanCommentAProduct_andCommentCountIncreases()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $this->assertEquals(0, $product->comments()->count());

        $response = $this->post(route('comments.store', ['item' => $product->id]), [
            'comment' => 'これはコメントです。',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'comment' => 'これはコメントです。',
        ]);

        // コメント数が1に増えていることを確認
        $this->assertEquals(1, $product->fresh()->comments()->count());
    }

    // ログイン前のユーザーはコメントを送信できない
    public function test_guestCannotPostCommentAndIsRedirectedToLogin()
    {
        $product = \App\Models\Product::factory()->create();

        $response = $this->post(route('comments.store', ['item' => $product->id]), [
            'comment' => 'ゲストコメント',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('comments', [
            'comment' => 'ゲストコメント',
            'product_id' => $product->id,
        ]);
    }

    // コメントが入力されていない場合、バリデーションメッセージが表示される
    public function test_commentIsRequiredToPost()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $product = \App\Models\Product::factory()->create();

        $response = $this->post(route('comments.store', ['item' => $product->id]), [
            'comment' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['comment']); 

        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    // コメントが255字以上の場合、バリデーションメッセージが表示される
    public function test_commentCannotExceedMaxLength()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create();

        $longComment = str_repeat('あ', 256);

        $response = $this->post(route('comments.store', ['item' => $product->id]), [
            'comment' => $longComment,
        ]);

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['comment']);

        $this->assertDatabaseMissing('comments', [
            'comment' => $longComment,
        ]);
    }


}
