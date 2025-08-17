<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ID01_RegisterFeatureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function name_required_validation_message_is_shown(): void
    {
        $this->get('/register')->assertOk()->assertViewIs('auth.register');

        $this->from('/register')->post('/register', [
            'user_name'             => '',
            'email'                 => 'test@example.com',
            'password'              => 'secret1234',
            'password_confirmation' => 'secret1234',
        ])
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
            'password' => 'パスワードと一致しません',
        ]);
    }

    #[Test]
    public function success_creates_user_and_redirects_to_verification_notice(): void
    {
        $this->get('/register')->assertOk();

        $response = $this->post('/register', [
            'user_name'             => 'テスト太郎',
            'email'                 => 'test@example.com',
            'password'              => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        // RegisterdUserController@store は verification.notice へ
        $response->assertRedirect(route('verification.notice'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'user_name' => 'テスト太郎',
        ]);

        // コントローラで Auth::login 済み
        $this->assertAuthenticated();
    }
}
