<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID01_RegisterFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID01: 会員登録機能
     * 仕様：
     *  - 必須項目の未入力時は各バリデーションメッセージを表示し、/register にリダイレクト
     *  - パスワードは8文字以上
     *  - 確認用パスワードと一致しない場合はエラーメッセージ
     *  - 正常系：登録成功後はログイン画面（/login）に遷移し、未ログインのまま
     */
    #[Test]
    public function name_required_validation_message_is_shown(): void
    {
        // 1. 会員登録ページを開く
        $this->get('/register')->assertOk()->assertViewIs('auth.register');

        // 2. 名前を空にして他の項目を入力 → 3. 登録ボタン押下
        $this->from('/register')->post('/register', [
            'user_name'             => '',
            'email'                 => 'test@example.com',
            'password'              => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])
        // 期待：/register にリダイレクト & メッセージ
        ->assertRedirect('/register')
        ->assertSessionHasErrors([
            'user_name' => 'お名前を入力してください',
        ]);
    }

    #[Test]
    public function email_required_validation_message_is_shown(): void
    {
        $this->get('/register')->assertOk();

        $this->from('/register')->post('/register', [
            'user_name'             => 'テスト太郎',
            'email'                 => '',
            'password'              => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])
        ->assertRedirect('/register')
        ->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    #[Test]
    public function password_required_validation_message_is_shown(): void
    {
        $this->get('/register')->assertOk();

        $this->from('/register')->post('/register', [
            'user_name'             => 'テスト太郎',
            'email'                 => 'test@example.com',
            'password'              => '',
            'password_confirmation' => 'secret1234',
        ])
        ->assertRedirect('/register')
        ->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    #[Test]
    public function password_min_length_validation_message_is_shown(): void
    {
        $this->get('/register')->assertOk();

        $this->from('/register')->post('/register', [
            'user_name'             => 'テスト太郎',
            'email'                 => 'test@example.com',
            'password'              => '1234567', // 7文字
            'password_confirmation' => '1234567',
        ])
        ->assertRedirect('/register')
        ->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    #[Test]
    public function password_confirmation_mismatch_shows_validation_message(): void
    {
        $this->get('/register')->assertOk();

        $this->from('/register')->post('/register', [
            'user_name'             => 'テスト太郎',
            'email'                 => 'test@example.com',
            'password'              => 'secret1234',
            'password_confirmation' => 'different1234',
        ])
        ->assertRedirect('/register')
        ->assertSessionHasErrors([
            // confirmed ルールのメッセージが password に付く想定
            'password' => 'パスワードと一致しません',
        ]);
    }

    #[Test]
    public function success_creates_user_and_redirects_to_verification_notice_and_authenticated(): void
    {
        $this->get('/register')->assertOk();

        $response = $this->post('/register', [
            'user_name'             => 'テスト太郎',
            'email'                 => 'test@example.com',
            'password'              => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        // DB登録される
        $this->assertDatabaseHas('users', [
            'email'     => 'test@example.com',
            'user_name' => 'テスト太郎',
        ]);

        // Fortify既定挙動：メール認証案内にリダイレクト
        $response->assertRedirect(route('verification.notice'));

        // 既定では登録後にログイン状態になる
        $this->assertAuthenticated();
    }

}
