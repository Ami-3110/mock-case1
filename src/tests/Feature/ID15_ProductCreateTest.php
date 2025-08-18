<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class ID15_ProductCreateTest extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    /** 1x1 PNG のバイナリを一時ファイルに書き出してパスを返す（GD不要） */
    private function createTempPngPath(): string
    {
        $png1x1 = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGMAAQAABQABqotWAAAAAElFTkSuQmCC'
        );
        $path = sys_get_temp_dir() . '/t_' . uniqid() . '.png';
        file_put_contents($path, $png1x1);
        return $path;
    }

    #[Test]
    public function submitting_create_form_saves_required_fields_and_categories_with_image()
    {
        Storage::fake('public'); // publicディスクをモック

        $user = $this->makeVerifiedUser();
        $this->actingAs($user);

        // カテゴリを2つ用意
        $cat1 = Category::factory()->create(['category_name' => 'カテゴリA']);
        $cat2 = Category::factory()->create(['category_name' => 'カテゴリB']);

        // ←←← ここがポイント：GD不要の実ファイルを使う（image()は禁止）
        $tmpPath   = $this->createTempPngPath();
        $fakeImage = new UploadedFile(
            $tmpPath,            // 実ファイルパス
            'product.png',       // クライアントファイル名
            'image/png',         // MIME
            null,
            true                 // テスト用 = アップロード済み扱い
        );

        $payload = [
            'category'     => [$cat1->id, $cat2->id],
            'condition'    => '良好',
            'product_name' => 'テスト出品タイトル',
            'brand'        => 'ブランド名（任意）',
            'description'  => '説明テキストです。',
            'price'        => 19800,
            'product_image'=> $fakeImage,
        ];

        $res = $this->post(route('items.store'), $payload);

        $res->assertStatus(302);
        $res->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', [
            'product_name' => 'テスト出品タイトル',
            'description'  => '説明テキストです。',
            'price'        => 19800,
            'condition'    => '良好',
            'user_id'      => $user->id,
        ]);

        $product = Product::where('product_name', 'テスト出品タイトル')->firstOrFail();

        Storage::disk('public')->assertExists($product->product_image);

        $this->assertDatabaseHas('product_category', [
            'product_id'  => $product->id,
            'category_id' => $cat1->id,
        ]);
        $this->assertDatabaseHas('product_category', [
            'product_id'  => $product->id,
            'category_id' => $cat2->id,
        ]);
    }
}

