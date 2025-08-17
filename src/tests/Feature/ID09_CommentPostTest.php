<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID09_CommentPostTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_post_comment()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $payload = ['comment' => 'これはテストコメントです。'];

        $res = $this->post(route('comments.store', ['item' => $product->id]), $payload);

        // 成功時は基本302で詳細ページへ戻す想定（先は固定しない）
        $res->assertStatus(302);

        // DBに作成されていること
        $this->assertDatabaseHas('comments', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
            'comment'    => 'これはテストコメントです。',
        ]);

        // 画面に表示されること（任意：詳細ページへGETして確認）
        $show = $this->get(route('item.show', ['item_id' => $product->id]));
        $show->assertSuccessful()
             ->assertSee(e('これはテストコメントです。'))
             ->assertSee(e($user->name));
    }

    #[Test]
    public function guest_cannot_post_comment()
    {
        $product = Product::factory()->create();

        $res = $this->post(route('comments.store', ['item' => $product->id]), [
            'comment' => 'ゲストのコメント',
        ]);

        // 未ログインは /login に飛ばす想定
        $res->assertRedirect('/login');

        // DBに作られていないこと
        $this->assertDatabaseMissing('comments', [
            'product_id' => $product->id,
            'comment'    => 'ゲストのコメント',
        ]);
    }

    #[Test]
    public function content_is_required_validation_message()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $res = $this->from(route('item.show', ['item_id' => $product->id]))
                    ->post(route('comments.store', ['item' => $product->id]), [
                        'comment' => '',
                    ]);

        $res->assertStatus(302)
            ->assertSessionHasErrors(['comment']);

        $this->assertDatabaseMissing('comments', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }

    #[Test]
    public function content_must_be_255_chars_or_less()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $tooLong = str_repeat('あ', 256);

        $res = $this->from(route('item.show', ['item_id' => $product->id]))
                    ->post(route('comments.store', ['item' => $product->id]), [
                        'comment' => $tooLong,
                    ]);

        $res->assertStatus(302)
            ->assertSessionHasErrors(['comment']);

        $this->assertDatabaseMissing('comments', [
            'user_id'    => $user->id,
            'product_id' => $product->id,
        ]);
    }
}
