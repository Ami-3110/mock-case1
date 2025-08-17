<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID14_UserEditPrefillTest extends TestCase
{
    use RefreshDatabase;

    private function makeVerifiedUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function edit_page_prefills_profile_image_user_name_postal_code_and_address()
    {
        // 1) ユーザー作成（verified）＋プロフィール作成
        $user = $this->makeVerifiedUser();
        $user->user_name = 'Ami Tester';
        $user->save();

        $user->userProfile()->create([
            'profile_image' => 'profiles/test-avatar.jpg', // nullable だが今回は有りで
            'postal_code'   => '100-0001',
            'address'       => '東京都千代田区千代田1-1',
            'building'      => 'テストビル201',
        ]);

        // 2) ログインして編集画面へ
        $this->actingAs($user);
        $res = $this->get(route('mypage.edit'));
        $res->assertSuccessful();

        // 3) 初期値がHTML上に存在することを検証
        // (a) プロフィール画像（imgのsrcにstorage/…の断片が含まれる）
        $res->assertSee('storage/profiles/test-avatar.jpg');

        // (b) ユーザー名 input の value
        $res->assertSee('value="Ami Tester"', false);

        // (c) 郵便番号 / 住所 / 建物名 input の value
        $res->assertSee('value="100-0001"', false);
        $res->assertSee('value="東京都千代田区千代田1-1"', false);
        $res->assertSee('value="テストビル201"', false);

        // 見出しなど基本UIも一応
        $res->assertSee('プロフィール設定');
        $res->assertSee('ユーザー名');
        $res->assertSee('郵便番号');
        $res->assertSee('住所');
        $res->assertSee('建物名');
    }
}
